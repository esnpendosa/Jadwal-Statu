<?php

return [
    'API_HOST' => env('WAHA_API_HOST', 'http://localhost:3000'),
    'API_KEY' => env('WAHA_API_KEY', ''),
    'BASIC_AUTH_USER' => env('WAHA_BASIC_AUTH_USER', ''),
    'BASIC_AUTH_PASSWORD' => env('WAHA_BASIC_AUTH_PASSWORD', ''),

    // Konfigurasi session bawaan
    'SESSION' => env('WAHA_SESSION', 'default'),

    // Konfigurasi rate limit
    'RATE_LIMIT' => [
        'MAX_ATTEMPTS' => env('WAHA_RATE_LIMIT_MAX_ATTEMPTS', 5),
        'DECAY_MINUTES' => env('WAHA_RATE_LIMIT_DECAY_MINUTES', 1),
        'MAX_WAIT_TIME' => env('WAHA_RATE_LIMIT_MAX_WAIT_TIME', 300),
    ],
];
