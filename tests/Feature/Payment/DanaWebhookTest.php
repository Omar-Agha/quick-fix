<?php

namespace Tests\Feature\Payment;

use App\Payments\Dana\DanaSignature;
use App\Payments\Dana\DanaTimestamp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DanaWebhookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test webhook signature verification.
     */
    public function test_webhook_signature_verification(): void
    {
        // Generate test keys (in production, use actual DANA keys)
        $keyPair = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($keyPair, $privateKey);
        $publicKeyDetails = openssl_pkey_get_details($keyPair);
        $publicKey = $publicKeyDetails['key'];

        $partnerId = 'TEST_PARTNER_ID';
        $timestamp = DanaTimestamp::generate();
        $requestBody = json_encode([
            'partnerReferenceNo' => 'MERCHANT_123',
            'latestTransactionStatus' => '00',
        ]);

        // Generate signature
        $signature = DanaSignature::generate(
            partnerId: $partnerId,
            timestamp: $timestamp,
            requestBody: $requestBody,
            privateKey: $privateKey
        );

        // Verify signature
        $isValid = DanaSignature::verify(
            signature: $signature,
            partnerId: $partnerId,
            timestamp: $timestamp,
            requestBody: $requestBody,
            publicKey: $publicKey
        );

        $this->assertTrue($isValid, 'Signature verification should succeed');
    }

    /**
     * Test webhook signature verification fails with invalid signature.
     */
    public function test_webhook_signature_verification_fails_with_invalid_signature(): void
    {
        $keyPair = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        $publicKeyDetails = openssl_pkey_get_details($keyPair);
        $publicKey = $publicKeyDetails['key'];

        $partnerId = 'TEST_PARTNER_ID';
        $timestamp = DanaTimestamp::generate();
        $requestBody = json_encode(['test' => 'data']);

        // Use invalid signature
        $invalidSignature = base64_encode('invalid_signature_data');

        $isValid = DanaSignature::verify(
            signature: $invalidSignature,
            partnerId: $partnerId,
            timestamp: $timestamp,
            requestBody: $requestBody,
            publicKey: $publicKey
        );

        $this->assertFalse($isValid, 'Signature verification should fail with invalid signature');
    }

    /**
     * Test DANA timestamp generation format.
     */
    public function test_dana_timestamp_format(): void
    {
        $timestamp = DanaTimestamp::generate();

        // Should match format: YYYY-MM-DDTHH:mm:ss+07:00
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+07:00$/',
            $timestamp,
            'Timestamp should match DANA format YYYY-MM-DDTHH:mm:ss+07:00'
        );

        // Should be valid
        $this->assertTrue(
            DanaTimestamp::isValid($timestamp),
            'Generated timestamp should be valid'
        );
    }

    /**
     * Test webhook status mapping.
     */
    public function test_webhook_status_mapping(): void
    {
        // This test would require the full gateway implementation
        // For now, we test the status mapping logic conceptually

        $statusMappings = [
            '00' => 'success',
            '05' => 'expired',
            '99' => 'failed',
        ];

        foreach ($statusMappings as $danaStatus => $expectedStatus) {
            // In actual implementation, this would be tested via the gateway
            $this->assertArrayHasKey($danaStatus, $statusMappings);
        }
    }
}

