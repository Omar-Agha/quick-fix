<?php

namespace App\Payments\Midtrans;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HTTP client for Midtrans Snap API.
 *
 * @see https://docs.midtrans.com/docs/snap-snap-integration-guide
 * @see https://docs.midtrans.com/docs/api-authorization-headers
 */
class MidtransClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $serverKey,
    ) {}

    /**
     * Build Basic Auth header value.
     * Format: Base64(ServerKey + ":"). Password is empty.
     */
    private function authHeader(): string
    {
        $credentials = $this->serverKey.':';

        return 'Basic '.base64_encode($credentials);
    }

    /**
     * Create Snap transaction and return token + redirect_url.
     *
     * POST /snap/v1/transactions
     *
     * @param  array<string, mixed>  $params  transaction_details (order_id, gross_amount), customer_details, etc.
     * @return array{token?: string, redirect_url?: string, error_messages?: array}
     */
    public function createSnapTransaction(array $params): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $this->authHeader(),
        ])->post("{$this->baseUrl}/snap/v1/transactions", $params);

        $data = $response->json();

        if (! $response->successful()) {
            Log::error('Midtrans: Create Snap transaction failed', [
                'status' => $response->status(),
                'response' => $data,
                'order_id' => $params['transaction_details']['order_id'] ?? null,
            ]);

            return [
                'error_messages' => $data['error_messages'] ?? [$data['message'] ?? 'Unknown error'],
            ];
        }

        return $data;
    }

    /**
     * Get transaction status (for verifying webhook / challenge response).
     *
     * GET /v2/{order_id}/status
     *
     * @see https://docs.midtrans.com/reference/get-transaction-status
     */
    public function getTransactionStatus(string $orderId): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $this->authHeader(),
        ])->get("{$this->baseUrl}/v2/{$orderId}/status");

        if (! $response->successful()) {
            Log::warning('Midtrans: Get transaction status failed', [
                'order_id' => $orderId,
                'status' => $response->status(),
            ]);

            return [];
        }

        return $response->json();
    }
}
