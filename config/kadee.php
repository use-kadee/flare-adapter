<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kadee Project ID
    |--------------------------------------------------------------------------
    |
    | Your Kadee project identifier (UUID). Get this from your Kadee dashboard.
    |
    */
    'project' => env('KADEE_PROJECT'),

    /*
    |--------------------------------------------------------------------------
    | Kadee Secret Key
    |--------------------------------------------------------------------------
    |
    | Your Kadee project secret for signing requests. Get this from your
    | Kadee dashboard. This is used to authenticate error reports.
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
    'endpoint' => env('KADEE_ENDPOINT', 'https://usekadee.com/api/ingest'),

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
