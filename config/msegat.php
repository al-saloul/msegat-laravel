<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gateway credentials
    |--------------------------------------------------------------------------
    |
    | These are issued by Msegat. The username, sender name and API key are all
    | required; sending fails fast with a MsegatException when one is missing.
    |
    */

    'base_url' => env('MSEGAT_BASEURL', 'https://www.msegat.com'),

    'username' => env('MSEGAT_USERNAME', ''),

    'user_sender' => env('MSEGAT_USER_SENDER', ''),

    'api_key' => env('MSEGAT_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | HTTP behaviour
    |--------------------------------------------------------------------------
    |
    | "timeout" is the per request limit in seconds. "retries" is the number of
    | extra attempts made when the gateway cannot be reached (0 disables them),
    | and "retry_delay" is the pause between those attempts in milliseconds.
    |
    */

    'timeout' => (int) env('MSEGAT_TIMEOUT', 30),

    'retries' => (int) env('MSEGAT_RETRIES', 0),

    'retry_delay' => (int) env('MSEGAT_RETRY_DELAY', 250),
];
