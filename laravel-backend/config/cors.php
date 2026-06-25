<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | for your application. These settings determine which cross-origin
    | operations may execute in web browsers.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://127.0.0.1:5003'),
        env('APP_URL', 'http://127.0.0.1:8001'),
    ],

    'allowed_origins_patterns' => ['/^https?:\/\/localhost(:[0-9]+)?$/', '/^https?:\/\/127\.0\.0\.1(:[0-9]+)?$/'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
