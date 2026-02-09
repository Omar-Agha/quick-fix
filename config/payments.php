<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for various payment gateways integrated in the system.
    |
    */

    'default' => env('PAYMENT_GATEWAY_DEFAULT', 'dana'),

    'dana' => [
        /*
        |--------------------------------------------------------------------------
        | DANA Hosted Checkout Configuration
        |--------------------------------------------------------------------------
        |
        | Configuration for DANA payment gateway integration.
        | All values should be set in your .env file.
        |
        */

        // Base URL for DANA API (sandbox or production)
        'base_url' => env('DANA_BASE_URL', 'https://api-sandbox.dana.id'),

        // Partner ID (from DANA dashboard)
        'partner_id' => env('DANA_PARTNER_ID'),

        // Merchant ID (from DANA dashboard)
        'merchant_id' => env('DANA_MERCHANT_ID'),

        // Channel ID (from DANA dashboard)
        'channel_id' => env('DANA_CHANNEL_ID'),

        // Origin (your application origin)
        'origin' => env('DANA_ORIGIN', env('APP_URL')),

        // Private key for signing requests (PEM format)
        // Store in .env as multiline string or use file path
        'private_key' => env('DANA_PRIVATE_KEY'),

        // Public key for verifying webhook signatures (PEM format)
        // Store in .env as multiline string or use file path
        'public_key' => env('DANA_PUBLIC_KEY'),

        // Return URL - where DANA redirects customer after payment
        'return_url' => env('DANA_RETURN_URL', env('APP_URL').'/api/payments/dana/return'),

        // Notify URL - webhook endpoint for server-to-server notifications
        'notify_url' => env('DANA_NOTIFY_URL', env('APP_URL').'/api/payments/dana/webhook/finish-notify'),
    ],

    'midtrans' => [
        /*
        |--------------------------------------------------------------------------
        | Midtrans Snap Configuration
        |--------------------------------------------------------------------------
        |
        | Configuration for Midtrans Snap (built-in checkout / redirect).
        | @see https://docs.midtrans.com/docs/snap-snap-integration-guide
        |
        */

        // Base URL: Sandbox or Production
        'base_url' => env('MIDTRANS_BASE_URL', 'https://app.sandbox.midtrans.com'),

        // Server Key (from Midtrans Dashboard > Settings > Access Keys)
        'server_key' => env('MIDTRANS_SERVER_KEY', ''),

        // Client Key (for frontend Snap.js; optional for backend-only redirect flow)
        'client_key' => env('MIDTRANS_CLIENT_KEY'),

        // Whether to use production (accept real payments)
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    ],
];
