<?php

namespace App\Payments\Dana;

use App\Payments\Dana\DanaKeyHelper;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DanaClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $partnerId,
        private readonly string $merchantId,
        private readonly string $channelId,
        private readonly string $origin,
        private readonly string $privateKey,
    ) {}

    /**
     * Create a payment order via DANA Hosted Checkout API.
     * 
     * Endpoint: POST /payment-gateway/v1.0/debit/payment-host-to-host.htm
     * 
     * @param array $payload Request payload
     * @return array Response data
     * @throws \Exception
     */
    public function createPaymentOrder(array $payload): array
    {
        $timestamp = DanaTimestamp::generate();
        $requestBody = json_encode($payload, JSON_UNESCAPED_SLASHES);

        // Load private key (supports both file path and direct content)
        $privateKey = DanaKeyHelper::loadKey($this->privateKey);

        // Generate signature
        $signature = DanaSignature::generate(
            partnerId: $this->partnerId,
            timestamp: $timestamp,
            requestBody: $requestBody,
            privateKey: $privateKey
        );

        // Make request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-PARTNER-ID' => $this->partnerId,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-CHANNEL-ID' => $this->channelId,
            'X-ORIGIN' => $this->origin,
        ])->post("{$this->baseUrl}/payment-gateway/v1.0/debit/payment-host-to-host.htm", $payload);

        $responseData = $response->json();

        if (!$response->successful()) {
            Log::error('DANA: Create payment order failed', [
                'status' => $response->status(),
                'response' => $responseData,
                'payload' => $payload,
            ]);

            throw new \Exception(
                'DANA payment order creation failed: ' . ($responseData['responseMessage'] ?? 'Unknown error'),
                $response->status()
            );
        }

        return $responseData;
    }

    /**
     * Get configured HTTP client with default headers.
     */
    private function httpClient(): PendingRequest
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-PARTNER-ID' => $this->partnerId,
            'X-CHANNEL-ID' => $this->channelId,
            'X-ORIGIN' => $this->origin,
        ]);
    }
}
