<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\Dana\DanaClient;
use App\Payments\Dana\DanaKeyHelper;
use App\Payments\Dana\DanaSignature;
use App\Payments\Dana\DanaTimestamp;
use App\Payments\DTOs\PaymentInitResult;
use App\Payments\DTOs\PaymentReturnResult;
use App\Payments\DTOs\PaymentReturnStatus;
use App\Payments\DTOs\WebhookResult;
use App\Payments\DTOs\WebhookStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DanaHostedCheckoutGateway implements PaymentGateway
{
    public function __construct(
        private readonly DanaClient $client,
        private readonly string $publicKey,
        private readonly string $returnUrl,
        private readonly string $notifyUrl,
    ) {}

    /**
     * Create payment intent for DANA Hosted Checkout.
     * 
     * Generates partnerReferenceNo from order ID for idempotency.
     * Returns webRedirectUrl for customer to complete payment.
     */
    public function createPaymentIntent(Order $order): PaymentInitResult
    {
        try {
            // Calculate total amount (total_cost + fees - discount)
            // $totalAmount = $order->total_cost + $order->fees - ($order->discount ?? 0);
            $totalAmount = $order->pay_at_cashier;

            // Generate partner reference number (idempotency key: merchantId + orderId)
            $partnerReferenceNo = config('payments.dana.merchant_id') . '_' . $order->id;

            // Build request payload as per DANA documentation
            $payload = [
                'partnerReferenceNo' => $partnerReferenceNo,
                'merchantId' => config('payments.dana.merchant_id'),
                'amount' => [
                    'value' => number_format($totalAmount, 2, '.', ''),
                    'currency' => 'IDR',
                ],
                'orderTitle' => "Order #{$order->id}",
                'orderDetail' => $order->description ?? "Payment for order #{$order->id}",
                'redirectUrl' => $this->returnUrl,
                'notifyUrl' => $this->notifyUrl,
            ];

            // Call DANA API
            $response = $this->client->createPaymentOrder($payload);

            // Extract webRedirectUrl from response
            if (isset($response['webRedirectUrl']) && !empty($response['webRedirectUrl'])) {
                // TODO: Persist payment_reference_no to payments table
                // Example: $order->payment_reference_no = $response['referenceNo'] ?? $partnerReferenceNo;
                // Example: $order->payment_gateway = 'dana';
                // Example: $order->save();

                return PaymentInitResult::success(
                    redirectUrl: $response['webRedirectUrl'],
                    paymentReference: $response['referenceNo'] ?? $partnerReferenceNo,
                    rawResponse: $response,
                );
            }

            return PaymentInitResult::failure(
                errorMessage: $response['responseMessage'] ?? 'Failed to get redirect URL from DANA',
                rawResponse: $response,
            );
        } catch (\Exception $e) {
            Log::error('DANA: Payment intent creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return PaymentInitResult::failure(
                errorMessage: 'Failed to create payment: ' . $e->getMessage(),
            );
        }
    }

    /**
     * Handle customer return from DANA hosted checkout.
     * 
     * DANA redirects with query parameters:
     * - resultCode: Payment result code
     * - partnerReferenceNo: Our reference number
     * - referenceNo: DANA transaction reference
     */
    public function handleRedirectReturn(Request $request): PaymentReturnResult
    {
        $resultCode = $request->query('resultCode');
        $partnerReferenceNo = $request->query('partnerReferenceNo');
        $referenceNo = $request->query('referenceNo');

        // Extract order ID from partnerReferenceNo (format: merchantId_orderId)
        $orderId = $this->extractOrderIdFromPartnerReference($partnerReferenceNo);

        if (!$orderId) {
            return PaymentReturnResult::failed(
                orderId: '',
                message: 'Invalid partner reference number',
                rawData: $request->query(),
            );
        }

        // Map DANA result codes to our status
        // Based on DANA documentation, resultCode values may vary
        // Common values: SUCCESS, FAILED, CANCEL, etc.
        $status = match (strtoupper($resultCode ?? '')) {
            'SUCCESS', '00' => PaymentReturnStatus::SUCCESS,
            'CANCEL', 'CANCELLED' => PaymentReturnStatus::CANCELLED,
            'EXPIRED', '05' => PaymentReturnStatus::EXPIRED,
            default => PaymentReturnStatus::PENDING, // Wait for webhook for final status
        };

        // TODO: Update order payment status based on return status
        // Example: if status is SUCCESS, mark as pending verification until webhook confirms
        // Example: if status is CANCELLED/EXPIRED, mark accordingly

        return match ($status) {
            PaymentReturnStatus::SUCCESS => PaymentReturnResult::success(
                orderId: (string) $orderId,
                paymentReference: $referenceNo,
                rawData: $request->query(),
            ),
            PaymentReturnStatus::CANCELLED => PaymentReturnResult::cancelled(
                orderId: (string) $orderId,
                paymentReference: $referenceNo,
                rawData: $request->query(),
            ),
            PaymentReturnStatus::EXPIRED => PaymentReturnResult::expired(
                orderId: (string) $orderId,
                paymentReference: $referenceNo,
                rawData: $request->query(),
            ),
            default => PaymentReturnResult::pending(
                orderId: (string) $orderId,
                paymentReference: $referenceNo,
                rawData: $request->query(),
            ),
        };
    }

    /**
     * Handle DANA Finish Notify webhook.
     * 
     * Endpoint: POST /v1.0/debit/notify
     * 
     * Verifies signature and processes payment status update.
     * Status codes:
     * - 00: Success
     * - 05: Cancelled/Expired
     */
    public function handleWebhook(Request $request): WebhookResult
    {
        // Extract required headers
        $partnerId = $request->header('X-PARTNER-ID');
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');

        if (!$partnerId || !$timestamp || !$signature) {
            Log::warning('DANA: Missing required headers in webhook', [
                'headers' => $request->headers->all(),
            ]);

            return WebhookResult::unverified(
                message: 'Missing required headers (X-PARTNER-ID, X-TIMESTAMP, X-SIGNATURE)',
                rawPayload: $request->all(),
            );
        }

        // Get request body as JSON string
        $requestBody = $request->getContent();

        // Load public key (supports both file path and direct content)
        $publicKey = DanaKeyHelper::loadKey($this->publicKey);

        // Verify signature
        $isValid = DanaSignature::verify(
            signature: $signature,
            partnerId: $partnerId,
            timestamp: $timestamp,
            requestBody: $requestBody,
            publicKey: $publicKey,
        );

        if (!$isValid) {
            Log::warning('DANA: Webhook signature verification failed', [
                'partner_id' => $partnerId,
                'timestamp' => $timestamp,
            ]);

            return WebhookResult::unverified(
                message: 'Signature verification failed',
                rawPayload: $request->all(),
            );
        }

        // Parse webhook payload
        $payload = $request->json()->all();

        // Extract order ID from partnerReferenceNo
        $partnerReferenceNo = $payload['partnerReferenceNo'] ?? null;
        $orderId = $this->extractOrderIdFromPartnerReference($partnerReferenceNo);

        if (!$orderId) {
            Log::warning('DANA: Could not extract order ID from webhook', [
                'partnerReferenceNo' => $partnerReferenceNo,
                'payload' => $payload,
            ]);

            return WebhookResult::unverified(
                message: 'Invalid partner reference number in webhook',
                rawPayload: $payload,
            );
        }

        // Map DANA transaction status to our webhook status
        $latestTransactionStatus = $payload['latestTransactionStatus'] ?? null;
        $status = match ($latestTransactionStatus) {
            '00' => WebhookStatus::SUCCESS,
            '05' => WebhookStatus::EXPIRED,
            default => WebhookStatus::FAILED,
        };

        // TODO: Implement idempotency check
        // Example: Check if webhook with this referenceNo has already been processed
        // Example: Store webhook_reference_no in payments table to prevent duplicate processing

        // TODO: Update order status based on webhook status
        // Example: if status is SUCCESS, update order status to CONFIRMED and mark payment as completed
        // Example: if status is EXPIRED, update order status to CANCELLED
        // Example: Store transaction_id, reference_no, and other payment details

        Log::info('DANA: Webhook processed successfully', [
            'order_id' => $orderId,
            'status' => $status->value,
            'reference_no' => $payload['referenceNo'] ?? null,
        ]);

        return WebhookResult::verified(
            status: $status,
            orderId: (string) $orderId,
            paymentReference: $payload['referenceNo'] ?? null,
            transactionId: $payload['transactionId'] ?? null,
            message: $payload['responseMessage'] ?? null,
            rawPayload: $payload,
        );
    }

    /**
     * Extract order ID from partner reference number.
     * Format: merchantId_orderId
     */
    private function extractOrderIdFromPartnerReference(?string $partnerReferenceNo): ?int
    {
        if (!$partnerReferenceNo) {
            return null;
        }

        // Format: merchantId_orderId
        $parts = explode('_', $partnerReferenceNo);

        if (count($parts) < 2) {
            return null;
        }

        // Last part should be the order ID
        $orderId = end($parts);

        return is_numeric($orderId) ? (int) $orderId : null;
    }
}
