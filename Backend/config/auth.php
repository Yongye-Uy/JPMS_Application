<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Backend has no local users table — the only guard is "remote-sanctum",
    | registered via Auth::viaRequest() in AppServiceProvider::boot(). It
    | resolves the Bearer token by asking API (which forwards to
    | Central-Service) rather than an Eloquent provider.
    |
    */

    'defaults' => [
        'guard' => 'remote-sanctum',
        'passwords' => null,
    ],

    'guards' => [
        'remote-sanctum' => [
            'driver' => 'remote-sanctum',
        ],
    ],

    'providers' => [],

    'passwords' => [],

    'password_timeout' => 10800,

];
