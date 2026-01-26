<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // FRONTEND_URL (vd: https://todokizamu.me), FRONTEND_URL_WWW (vd: https://www.todokizamu.me) từ .env
    'allowed_origins' => array_values(array_filter(array_merge(
        ['http://localhost:8088', 'http://localhost:3000'],
        [env('FRONTEND_URL', 'http://localhost:8088')],
        [env('FRONTEND_URL_WWW')] // tùy chọn: https://www.todokizamu.me
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
