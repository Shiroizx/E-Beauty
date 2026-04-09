<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DOKU Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    'client_id' => env('DOKU_CLIENT_ID', ''),

    'secret_key' => env('DOKU_SECRET_KEY', ''),

    'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com'),

    'environment' => env('DOKU_ENVIRONMENT', 'sandbox'),

    // Payment due in minutes (default 60 minutes = 1 hour)
    'payment_due_minutes' => env('DOKU_PAYMENT_DUE_MINUTES', 60),

];
