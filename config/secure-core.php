<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Encryption
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'enabled' => env('SECURE_CORE_ENCRYPTION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Strict Log Masking
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'masking_enabled' => true,
        'masked_fields' => [
            'password', 'password_confirmation', 'cvv', 'card_number', 'api_key', 'token', 'secret'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatic Security Headers
    |--------------------------------------------------------------------------
    */
    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-XSS-Protection' => '1; mode=block',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Content-Security-Policy' => "upgrade-insecure-requests",
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    */
    'api' => [
        'signature_check' => env('SECURE_CORE_SIGNATURE', true),
        'signature_secret' => env('SECURE_CORE_SIGNATURE_SECRET', env('APP_KEY')),
    ],
];