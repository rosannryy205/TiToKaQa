<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login', 'logout', 'user'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://titokaqarestaurant.online',
        'https://www.titokaqarestaurant.online',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
