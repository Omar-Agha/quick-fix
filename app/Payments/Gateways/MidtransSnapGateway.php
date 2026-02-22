<?php

namespace App\Payments\Gateways;

use App\Enums\OrderStatus as OrderStatusEnum;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\DTOs\PaymentInitResult;
use App\Payments\DTOs\PaymentReturnResult;

use App\Payments\DTOs\PaymentReturnStatus;
use App\Payments\DTOs\WebhookResult;
use App\Payments\DTOs\WebhookStatus;
use App\Payments\Midtrans\MidtransClient;
use App\Payments\Midtrans\MidtransSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Payments\DTOs;

/**
 * Midtrans Snap payment gateway (built-in checkout / redirect).
 *
 * @see https://docs.midtrans.com/docs/snap-snap-integration-guide
 * @see https://docs.midtrans.com/reference/get-transaction-status-1 (notifications)
 */
class MidtransSnapGateway implements PaymentGateway
{
    /** order_id prefix for Midtrans (allowed chars: alphanumeric, -, _, ~, . max 50) */
    private const ORDER_ID_PREFIX = 'order-';

    public function __construct(
        private readonly MidtransClient $client,
        private readonly string $serverKey,
    ) {}

    /**
     * Create Snap transaction and return redirect_url (and token for embedded/popup).
     * Uses pay_at_cashier as gross_amount.
     * order_id = "order-{id}" for first attempt, "order-{id}-{n}" for retries (expired/cancelled).
     */
    public function createPaymentIntent(Order $order): PaymentInitResult
    {

        try {
            $existingPayments = Payment::where('order_id', $order->id)->where('gateway', 'midtrans')->orderByDesc('id')->get();
            $latestPayment = $existingPayments->first();

            if ($latestPayment?->status === PaymentStatus::COMPLETED) {
                return PaymentInitResult::success(
                    redirectUrl: $latestPayment->redirect_url ?? '',
                    paymentReference: $latestPayment->gateway_reference,
                    rawResponse: ['already_paid' => true],
                );
            }

            $isPendingRecent = $latestPayment
                && $latestPayment->status === PaymentStatus::PENDING
                && $latestPayment->initiated_at?->isAfter(now()->subMinutes(15));

            if ($isPendingRecent) {
                return PaymentInitResult::success(
                    redirectUrl: $latestPayment->redirect_url ?? '',
                    paymentReference: $latestPayment->gateway_reference,
                    rawResponse: [],
                );
            }

            $attempt = $existingPayments->count() + 1;
            $midtransOrderId = $this->toMidtransOrderId($order->id, $attempt);
            $grossAmount = (int) round($order->pay_at_cashier);

            $params = [
                'transaction_details' => [
                    'order_id' => $midtransOrderId,
                    'gross_amount' => $grossAmount,
                ],
                'credit_card' => [
                    'secure' => true,
                ],
                'customer_details' => [
                    'first_name' => $order->mobileUser?->full_name ?? 'Customer',
                    'last_name' => '',
                    'email' => $order->mobileUser?->email ?? 'customer@example.com',
                    'phone' => $order->mobileUser?->phone_number ?? '',
                ],
            ];

            $response = $this->client->createSnapTransaction($params);

            if (isset($response['error_messages'])) {
                $message = is_array($response['error_messages'])
                    ? implode(', ', $response['error_messages'])
                    : (string) $response['error_messages'];

                return PaymentInitResult::failure(
                    errorMessage: $message,
                    rawResponse: $response,
                );
            }

            // Snap returns token and redirect_url; use redirect_url for redirect flow (e.g. Flutter/WebView)
            $redirectUrl = $response['redirect_url'] ?? null;
            $token = $response['token'] ?? null;

            if ($redirectUrl) {
                $order->payments()->create([
                    'gateway' => 'midtrans',
                    'gateway_reference' => $midtransOrderId,
                    'gateway_transaction_id' => null,
                    'status' => PaymentStatus::PENDING,
                    'amount' => $grossAmount,
                    'metadata' => $response,
                    'initiated_at' => now(),
                    'redirect_url' => $redirectUrl,
                ]);

                return PaymentInitResult::success(
                    redirectUrl: $redirectUrl,
                    paymentReference: $token ?? $midtransOrderId,
                    rawResponse: $response,
                );
            }

            return PaymentInitResult::failure(
                errorMessage: 'Failed to get redirect URL from Midtrans',
                rawResponse: $response,
            );
        } catch (\Exception $e) {
            Log::error('Midtrans: Payment intent creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return PaymentInitResult::failure(
                errorMessage: 'Failed to create payment: ' . $e->getMessage(),
            );
        }
    }

    /**
     * Handle customer return from Midtrans Snap (Finish URL).
     * Midtrans may append order_id (or similar) to the redirect; rely on webhook for final status.
     */
    public function handleRedirectReturn(Request $request): PaymentReturnResult
    {

        // Finish URL can contain order_id in query (e.g. ?order_id=order-123)
        $orderIdParam = $request->query('order_id');
        $internalOrderId = $this->extractInternalOrderId($orderIdParam);

        if ($internalOrderId === null && $orderIdParam !== null) {
            // order_id present but not our format
            $internalOrderId = $orderIdParam;
        }


        $statusParam = $request->query('status');
        $status = match (strtolower((string) $statusParam)) {
            'success', 'capture', 'settlement' => PaymentStatus::COMPLETED,
            'pending' => PaymentStatus::PENDING,
            'deny', 'cancel', 'expire' => PaymentStatus::FAILED,
            default => PaymentStatus::PENDING,
        };

        $orderId = $internalOrderId !== null ? (string) $internalOrderId : ($orderIdParam ?? '');

        return match ($status) {
            PaymentStatus::COMPLETED => PaymentReturnResult::success(
                orderId: $orderId,
                paymentReference: $request->query('transaction_id'),
                rawData: $request->query(),
            ),
            PaymentStatus::PENDING => PaymentReturnResult::pending(
                orderId: $orderId,
                message: null,
                rawData: $request->query(),
            ),
            default => PaymentReturnResult::failed(
                orderId: $orderId,
                message: $request->query('status_message', 'Payment failed'),
                rawData: $request->query(),
            ),
        };
    }

    /**
     * Handle HTTP notification (webhook) from Midtrans.
     * Verifies signature (SHA512 order_id+status_code+gross_amount+server_key) and maps transaction_status.
     */
    public function handleWebhook(Request $request): WebhookResult
    {
        $payload = $request->json()->all();

        $orderId = $payload['order_id'] ?? null;
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = $payload['signature_key'] ?? '';



        if (! $orderId || $signatureKey === '') {
            Log::warning('Midtrans: Webhook missing required fields', [
                'keys' => array_keys($payload),
            ]);

            return WebhookResult::unverified(
                message: 'Missing order_id or signature_key',
                rawPayload: $payload,
            );
        }

        if (! MidtransSignature::verify($orderId, $statusCode, $grossAmount, $signatureKey, $this->serverKey)) {
            return WebhookResult::unverified(
                message: 'Signature verification failed',
                rawPayload: $payload,
            );
        }

        // Optional: challenge response - call Get Status API to confirm (recommended in docs)
        // $statusResponse = $this->client->getTransactionStatus($orderId);
        // Use $statusResponse for final status if you want extra verification

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';


        $status = $this->mapTransactionStatusToWebhookStatus($transactionStatus, $fraudStatus, $statusCode);
        Log::info('Midtrans: Webhook received: transaction_status is :', ['transaction_status' => $transactionStatus, 'fraud_status' => $fraudStatus, 'statusCode' => $statusCode, 'status' => $status]);
        $internalOrderId = $this->extractInternalOrderId($orderId);

        $payment = $internalOrderId !== null
            ? Payment::where('order_id', $internalOrderId)->where('gateway', 'midtrans')->latest()->first()
            : Payment::where('gateway_reference', $orderId)->where('gateway', 'midtrans')->latest()->first();

        if (! $payment) {
            Log::warning('Midtrans: Webhook received for unknown payment', ['order_id' => $orderId]);

            return WebhookResult::verified(
                status: $status,
                orderId: $internalOrderId !== null ? (string) $internalOrderId : $orderId,
                paymentReference: $payload['transaction_id'] ?? null,
                transactionId: $payload['transaction_id'] ?? null,
                message: $payload['status_message'] ?? null,
                rawPayload: $payload,
            );
        }

        $order = $payment->order;
        $orderStatus = match ($status) {
            WebhookStatus::PENDING => OrderStatusEnum::PENDING,
            WebhookStatus::SUCCESS => OrderStatusEnum::SUCCESS,
            default => PaymentStatus::FAILED,
        };
        $order->update([
            'order_id' => $orderId,
            'status' => $orderStatus,
        ]);

        if ($payment->isFinal()) {
            Log::info('Midtrans: Webhook idempotent skip (already processed)', ['order_id' => $orderId, 'payment_status' => $payment->status->value]);

            return WebhookResult::verified(
                status: $status,
                orderId: (string) $payment->order_id,
                paymentReference: $payload['transaction_id'] ?? null,
                transactionId: $payload['transaction_id'] ?? null,
                message: $payload['status_message'] ?? null,
                rawPayload: $payload,
            );
        }

        $paymentStatus = match ($status) {
            WebhookStatus::PENDING => PaymentStatus::PENDING,
            WebhookStatus::SUCCESS => PaymentStatus::COMPLETED,
            WebhookStatus::EXPIRED => PaymentStatus::EXPIRED,
            default => PaymentStatus::FAILED,
        };
        Log::info('Midtrans: Webhook received: payment status is :', ['payment_status' => $paymentStatus]);

        $payment->update([
            'status' => $paymentStatus,
            'gateway_transaction_id' => $payload['transaction_id'] ?? $payment->gateway_transaction_id,
            'completed_at' => now(),
            'metadata' => array_merge($payment->metadata ?? [], ['webhook' => $payload]),
        ]);



        Log::info('Midtrans: Webhook processed', [
            'order_id' => $orderId,
            'internal_order_id' => $internalOrderId,
            'transaction_status' => $transactionStatus,
            'status' => $status->value,
        ]);

        return WebhookResult::verified(
            status: $status,
            orderId: $internalOrderId !== null ? (string) $internalOrderId : $orderId,
            paymentReference: $payload['transaction_id'] ?? null,
            transactionId: $payload['transaction_id'] ?? null,
            message: $payload['status_message'] ?? null,
            rawPayload: $payload,
        );
    }

    private function toMidtransOrderId(int $orderId, int $attempt = 1): string
    {
        if ($attempt === 1) {
            return self::ORDER_ID_PREFIX . $orderId;
        }

        return self::ORDER_ID_PREFIX . $orderId . '-' . $attempt;
    }

    private function extractInternalOrderId(?string $midtransOrderId): ?int
    {
        if ($midtransOrderId === null || $midtransOrderId === '') {
            return null;
        }
        if (str_starts_with($midtransOrderId, self::ORDER_ID_PREFIX)) {
            $rest = substr($midtransOrderId, strlen(self::ORDER_ID_PREFIX));
            $parts = explode('-', $rest);
            $first = $parts[0] ?? '';

            return is_numeric($first) ? (int) $first : null;
        }

        return is_numeric($midtransOrderId) ? (int) $midtransOrderId : null;
    }

    /**
     * Map Midtrans transaction_status (+ fraud_status, status_code) to WebhookStatus.
     * capture/settlement + status_code 200 = success; pending = pending; deny/expire/cancel = failed/expired.
     */
    private function mapTransactionStatusToWebhookStatus(string $transactionStatus, string $fraudStatus, string $statusCode): WebhookStatus
    {
        $t = strtolower($transactionStatus);
        $f = strtolower($fraudStatus);

        if ($t === 'capture') {
            if ($f === 'accept' || $statusCode === '200') {
                return WebhookStatus::SUCCESS;
            }

            return WebhookStatus::FAILED;
        }

        if ($t === 'settlement' && $statusCode === '200') {
            return WebhookStatus::SUCCESS;
        }

        if ($t === 'pending') {
            return WebhookStatus::PENDING;
        }

        if (in_array($t, ['deny', 'cancel', 'expire'], true)) {
            return $t === 'expire' ? WebhookStatus::EXPIRED : WebhookStatus::FAILED;
        }

        //TODO: TO BE CHECKED
        return WebhookStatus::PENDING;
    }
}
