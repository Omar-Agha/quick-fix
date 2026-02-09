<?php

namespace Tests\Feature\Payment;

use App\Payments\Midtrans\MidtransSignature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_signature_verification(): void
    {
        $serverKey = 'SB-Mid-server-abc123';
        $orderId = 'order-123';
        $statusCode = '200';
        $grossAmount = '100000.00';

        $signature = MidtransSignature::generate($orderId, $statusCode, $grossAmount, $serverKey);

        $this->assertNotEmpty($signature);
        $this->assertSame(128, strlen($signature)); // SHA512 hex length

        $valid = MidtransSignature::verify($orderId, $statusCode, $grossAmount, $signature, $serverKey);
        $this->assertTrue($valid);
    }

    public function test_notification_signature_fails_with_wrong_key(): void
    {
        $serverKey = 'SB-Mid-server-abc123';
        $orderId = 'order-123';
        $statusCode = '200';
        $grossAmount = '100000.00';

        $signature = MidtransSignature::generate($orderId, $statusCode, $grossAmount, $serverKey);
        $valid = MidtransSignature::verify($orderId, $statusCode, $grossAmount, $signature, 'wrong-server-key');

        $this->assertFalse($valid);
    }

    public function test_webhook_returns_400_when_signature_invalid(): void
    {
        $payload = [
            'order_id' => 'order-1',
            'status_code' => '200',
            'gross_amount' => '50000',
            'signature_key' => 'invalid-signature',
            'transaction_status' => 'pending',
        ];

        $response = $this->postJson('/api/payments/midtrans/webhook/notify', $payload);

        $response->assertStatus(400);
    }
}
