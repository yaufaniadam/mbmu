<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway
    |--------------------------------------------------------------------------
    |
    | Supported gateways: "manual", "fonnte", "wablas"
    |
    */
    'gateway' => env('WA_GATEWAY', 'manual'),

    'fonnte' => [
        'token' => env('FONNTE_TOKEN'),
    ],

    'wablas' => [
        'server' => env('WABLAS_SERVER', env('WA_SERVER')),
        'token' => env('WABLAS_TOKEN', env('WA_TOKEN')),
    ],

    'admin_wa' => env('ADMIN_WA'),
];