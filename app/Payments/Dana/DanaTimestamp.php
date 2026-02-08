<?php

namespace App\Payments\Dana;

use Carbon\Carbon;

class DanaTimestamp
{
    /**
     * Generate timestamp in Jakarta GMT+7 format: YYYY-MM-DDTHH:mm:ss+07:00
     * As required by DANA API documentation.
     */
    public static function generate(): string
    {
        // Create Carbon instance in Jakarta timezone (Asia/Jakarta = GMT+7)
        $jakartaTime = Carbon::now('Asia/Jakarta');

        // Format: YYYY-MM-DDTHH:mm:ss+07:00
        return $jakartaTime->format('Y-m-d\TH:i:s+07:00');
    }

    /**
     * Validate timestamp format matches DANA requirements.
     */
    public static function isValid(string $timestamp): bool
    {
        try {
            $parsed = Carbon::createFromFormat('Y-m-d\TH:i:s+07:00', $timestamp, 'Asia/Jakarta');
            return $parsed !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

