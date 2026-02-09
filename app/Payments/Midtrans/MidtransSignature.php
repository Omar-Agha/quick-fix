<?php

namespace App\Payments\Midtrans;

use Illuminate\Support\Facades\Log;

/**
 * Midtrans notification signature verification.
 *
 * Signature = SHA512(order_id + status_code + gross_amount + server_key)
 *
 * @see https://docs.midtrans.com/reference/get-transaction-status-1 (Signature Key section)
 */
class MidtransSignature
{
    /**
     * Verify that the notification payload was sent by Midtrans.
     *
     * @param  string  $orderId  order_id from notification
     * @param  string  $statusCode  status_code from notification
     * @param  string  $grossAmount  gross_amount from notification (string e.g. "100000.00")
     * @param  string  $signatureKey  signature_key from notification
     * @param  string  $serverKey  Merchant Server Key
     */
    public static function verify(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey,
        string $serverKey
    ): bool {
        $expected = self::generate($orderId, $statusCode, $grossAmount, $serverKey);

        $valid = hash_equals($expected, $signatureKey);

        if (! $valid) {
            Log::warning('Midtrans: Notification signature verification failed', [
                'order_id' => $orderId,
            ]);
        }

        return $valid;
    }

    /**
     * Generate signature key (for verification).
     *
     * SHA512(order_id + status_code + gross_amount + server_key)
     */
    public static function generate(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $serverKey
    ): string {
        $input = $orderId.$statusCode.$grossAmount.$serverKey;

        return openssl_digest($input, 'sha512');
    }
}
