<?php

namespace App\Payments\Gateways;

use App\Enums\OrderStatus as OrderStatusEnum;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\Dana\DanaClient;
use App\Payments\Dana\DanaKeyHelper;
use App\Payments\Dana\DanaSignature;
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
     * partnerReferenceNo = merchantId_orderId (first attempt) or merchantId_orderId-N (retries).
     * Returns webRedirectUrl for customer to complete payment.
     */
    public function createPaymentIntent(Order $order): PaymentInitResult
    {
        try {
            $totalAmount = $order->pay_at_cashier;
            $existingPayments = Payment::where('order_id', $order->id)->where('gateway', 'dana')->orderByDesc('id')->get();
            $latestPayment = $existingPayments->first();

            if ($latestPayment?->status === PaymentStatus::COMPLETED) {
                $redirectUrl = $latestPayment->metadata['webRedirectUrl'] ?? '';

                return PaymentInitResult::success(
                    redirectUrl: $redirectUrl,
                    paymentReference: $latestPayment->gateway_reference,
                    rawResponse: ['already_paid' => true],
                );
            }

            $isPendingRecent = $latestPayment
                && $latestPayment->status === PaymentStatus::PENDING
                && $latestPayment->initiated_at?->isAfter(now()->subMinutes(15));

            if ($isPendingRecent) {
                $redirectUrl = $latestPayment->metadata['webRedirectUrl'] ?? '';

                return PaymentInitResult::success(
                    redirectUrl: $redirectUrl,
                    paymentReference: $latestPayment->gateway_reference,
                    rawResponse: [],
                );
            }

            $attempt = $existingPayments->count() + 1;
            $partnerReferenceNo = $this->toPartnerReferenceNo($order->id, $attempt);

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
            if (isset($response['webRedirectUrl']) && ! empty($response['webRedirectUrl'])) {
                $paymentReference = $response['referenceNo'] ?? $partnerReferenceNo;

                $order->payments()->create([
                    'gateway' => 'dana',
                    'gateway_reference' => $partnerReferenceNo,
                    'gateway_transaction_id' => $response['referenceNo'] ?? null,
                    'status' => PaymentStatus::PENDING,
                    'amount' => $totalAmount,
                    'metadata' => $response,
                    'initiated_at' => now(),
                ]);

                return PaymentInitResult::success(
                    redirectUrl: $response['webRedirectUrl'],
                    paymentReference: $paymentReference,
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

        if (! $orderId) {
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
            'SUCCESS', '00' => PaymentStatus::COMPLETED,
            'CANCEL', 'CANCELLED' => PaymentStatus::CANCELLED,
            'EXPIRED', '05' => PaymentStatus::EXPIRED,
            default => PaymentStatus::PENDING, // Wait for webhook for final status
        };

        $payment = Payment::where('order_id', $orderId)->where('gateway', 'dana')->latest()->first();
        if ($payment && ! $payment->isFinal()) {
            if ($status === PaymentStatus::CANCELLED || $status === PaymentStatus::EXPIRED) {
                $payment->update([
                    'status' => $status === PaymentStatus::CANCELLED ? PaymentStatus::CANCELLED : PaymentStatus::EXPIRED,
                    'gateway_transaction_id' => $referenceNo,
                    'completed_at' => now(),
                    'metadata' => array_merge($payment->metadata ?? [], ['redirect' => $request->query()]),
                ]);
                $payment->order->update(['status' => OrderStatusEnum::CANCELLED]);
            }
        }

        return match ($status) {
            PaymentStatus::COMPLETED => PaymentReturnResult::success(
                orderId: (string) $orderId,
                paymentReference: $referenceNo,
                rawData: $request->query(),
            ),
            PaymentStatus::CANCELLED => PaymentReturnResult::cancelled(
                orderId: (string) $orderId,
                message: null,
                rawData: $request->query(),
            ),
            PaymentStatus::EXPIRED => PaymentReturnResult::expired(
                orderId: (string) $orderId,
                message: null,
                rawData: $request->query(),
            ),
            PaymentStatus::PENDING => PaymentReturnResult::pending(
                orderId: (string) $orderId,
                message: null,
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

        if (! $partnerId || ! $timestamp || ! $signature) {
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

        if (! $isValid) {
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

        if (! $orderId) {
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

        $payment = Payment::where('order_id', $orderId)->where('gateway', 'dana')->latest()->first();
        if (! $payment) {
            Log::warning('DANA: Webhook received for unknown payment', ['order_id' => $orderId]);

            return WebhookResult::verified(
                status: $status,
                orderId: (string) $orderId,
                paymentReference: $payload['referenceNo'] ?? null,
                transactionId: $payload['transactionId'] ?? null,
                message: $payload['responseMessage'] ?? null,
                rawPayload: $payload,
            );
        }

        if ($payment->isFinal()) {
            Log::info('DANA: Webhook idempotent skip (already processed)', ['order_id' => $orderId, 'payment_status' => $payment->status->value]);

            return WebhookResult::verified(
                status: $status,
                orderId: (string) $orderId,
                paymentReference: $payload['referenceNo'] ?? null,
                transactionId: $payload['transactionId'] ?? null,
                message: $payload['responseMessage'] ?? null,
                rawPayload: $payload,
            );
        }

        $paymentStatus = match ($status) {
            WebhookStatus::SUCCESS => PaymentStatus::COMPLETED,
            WebhookStatus::EXPIRED => PaymentStatus::EXPIRED,
            default => PaymentStatus::FAILED,
        };
        $payment->update([
            'status' => $paymentStatus,
            'gateway_transaction_id' => $payload['referenceNo'] ?? $payment->gateway_transaction_id,
            'completed_at' => now(),
            'metadata' => array_merge($payment->metadata ?? [], ['webhook' => $payload]),
        ]);

        $order = $payment->order;
        $order->update([
            'status' => $status === WebhookStatus::SUCCESS ? OrderStatusEnum::CONFIRMED : OrderStatusEnum::CANCELLED,
        ]);

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

    private function toPartnerReferenceNo(int $orderId, int $attempt = 1): string
    {
        $base = config('payments.dana.merchant_id') . '_' . $orderId;

        if ($attempt === 1) {
            return $base;
        }

        return $base . '-' . $attempt;
    }

    /**
     * Extract order ID from partner reference number.
     * Format: merchantId_orderId or merchantId_orderId-attempt
     */
    private function extractOrderIdFromPartnerReference(?string $partnerReferenceNo): ?int
    {
        if (! $partnerReferenceNo) {
            return null;
        }

        $parts = explode('_', $partnerReferenceNo);

        if (count($parts) < 2) {
            return null;
        }

        $orderPart = $parts[1];
        $orderId = explode('-', $orderPart)[0] ?? '';

        return is_numeric($orderId) ? (int) $orderId : null;
    }
}
