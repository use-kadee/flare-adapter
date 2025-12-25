<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kadee Project Key
    |--------------------------------------------------------------------------
    |
    | Your Kadee project identifier. Get this from your Kadee dashboard.
    |
    */
    'key' => env('KADEE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Kadee API Endpoint
    |--------------------------------------------------------------------------
    |
    | The endpoint where error reports are sent.
    |
    */
    'endpoint' => env('KADEE_ENDPOINT', 'https://kadee.io/api/ingest'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Request timeout in seconds. Keep low to avoid slowing down the app.
    |
    */
    'timeout' => env('KADEE_TIMEOUT', 5),
];
