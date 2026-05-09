<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tripay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Mode: 'sandbox' or 'production'
    | Sandbox URL: https://tripay.co.id/api-sandbox
    | Production URL: https://tripay.co.id/api
    |
    */

    'mode' => env('TRIPAY_MODE', 'sandbox'),

    'api_key' => env('TRIPAY_API_KEY', ''),

    'private_key' => env('TRIPAY_PRIVATE_KEY', ''),

    'merchant_code' => env('TRIPAY_MERCHANT_CODE', ''),

    'base_url' => env('TRIPAY_MODE', 'sandbox') === 'production'
        ? 'https://tripay.co.id/api'
        : 'https://tripay.co.id/api-sandbox',

    // Callback URL — Tripay will POST payment notifications here
    'callback_url' => env('TRIPAY_CALLBACK_URL', ''),

    // Return URL — redirect pelanggan setelah bayar
    'return_url' => env('TRIPAY_RETURN_URL', ''),

    // Default expiry in seconds (24 hours)
    'expiry_seconds' => (int) env('TRIPAY_EXPIRY_SECONDS', 86400),
];
