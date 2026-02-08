<?php

namespace App\Payments\Dana;

use Illuminate\Support\Facades\Storage;

class DanaKeyHelper
{
    /**
     * Load private or public key from environment variable.
     * Supports both direct key content and file path.
     * 
     * @param string $keyValue Value from env (either key content or file path)
     * @return string Key content
     */
    public static function loadKey(string $keyValue): string
    {
        // If it's a file path (starts with storage/ or absolute path)
        if (str_starts_with($keyValue, 'storage/') || str_starts_with($keyValue, '/')) {
            $filePath = str_starts_with($keyValue, 'storage/')
                ? Storage::path(str_replace('storage/', '', $keyValue))
                : $keyValue;

            if (!file_exists($filePath)) {
                throw new \RuntimeException("DANA key file not found: {$filePath}");
            }

            return file_get_contents($filePath);
        }

        // Otherwise, treat as direct key content
        return $keyValue;
    }
}

