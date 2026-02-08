<?php

namespace App\Payments\Dana;

use Illuminate\Support\Facades\Log;

class DanaSignature
{
    /**
     * Generate X-SIGNATURE using asymmetric signature method as per DANA docs.
     * 
     * Signature is generated from:
     * - X-PARTNER-ID
     * - X-TIMESTAMP
     * - Request body (JSON string)
     * 
     * @param string $partnerId X-PARTNER-ID value
     * @param string $timestamp X-TIMESTAMP value (Jakarta GMT+7 format)
     * @param string $requestBody JSON string of request body
     * @param string $privateKey Private key for signing (PEM format)
     * @return string Base64 encoded signature
     */
    public static function generate(
        string $partnerId,
        string $timestamp,
        string $requestBody,
        string $privateKey
    ): string {
        // Construct signature string as per DANA documentation
        // Format: partnerId + timestamp + requestBody
        $signatureString = $partnerId . $timestamp . $requestBody;

        // Load private key
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        
        if ($privateKeyResource === false) {
            throw new \RuntimeException('Failed to load private key for DANA signature generation');
        }

        // Sign using SHA256 with RSA
        $signature = '';
        $success = openssl_sign($signatureString, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        openssl_free_key($privateKeyResource);

        if (!$success) {
            throw new \RuntimeException('Failed to generate DANA signature');
        }

        // Return base64 encoded signature
        return base64_encode($signature);
    }

    /**
     * Verify X-SIGNATURE from webhook/response.
     * 
     * @param string $signature Base64 encoded signature from X-SIGNATURE header
     * @param string $partnerId X-PARTNER-ID value
     * @param string $timestamp X-TIMESTAMP value
     * @param string $requestBody JSON string of request body
     * @param string $publicKey Public key for verification (PEM format)
     * @return bool True if signature is valid
     */
    public static function verify(
        string $signature,
        string $partnerId,
        string $timestamp,
        string $requestBody,
        string $publicKey
    ): bool {
        // Construct signature string (same as generation)
        $signatureString = $partnerId . $timestamp . $requestBody;

        // Decode base64 signature
        $decodedSignature = base64_decode($signature, true);
        
        if ($decodedSignature === false) {
            Log::warning('DANA: Failed to decode signature from base64', [
                'signature' => substr($signature, 0, 50) . '...',
            ]);
            return false;
        }

        // Load public key
        $publicKeyResource = openssl_pkey_get_public($publicKey);
        
        if ($publicKeyResource === false) {
            Log::warning('DANA: Failed to load public key for signature verification');
            return false;
        }

        // Verify signature
        $result = openssl_verify($signatureString, $decodedSignature, $publicKeyResource, OPENSSL_ALGO_SHA256);

        openssl_free_key($publicKeyResource);

        // openssl_verify returns 1 for valid, 0 for invalid, -1 for error
        return $result === 1;
    }
}

