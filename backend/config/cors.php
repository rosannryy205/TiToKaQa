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

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login', 'logout', 'user' // nếu dùng route web cho auth
    ],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://titokaqarestaurant.online',   // FE hiện tại
        // Khuyến nghị chuyển FE sang HTTPS:
        'https://titokaqarestaurant.online',
        'https://www.titokaqarestaurant.online',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
