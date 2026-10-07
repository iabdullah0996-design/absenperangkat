<?php

use App\Models\User;
use App\Models\Perangkat;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Dynamic default guard diset ke 'perangkat' agar saat user biasa belum login,
    | Laravel menggunakan guard 'perangkat' sebagai fallback acuan dasar.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'perangkat'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Terdiri dari 3 guard: 'web', 'perangkat' (user biasa/NIK), dan 'user' (admin).
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'perangkat' => [
            'driver' => 'session',
            'provider' => 'perangkat',
        ],

        'user' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Menghubungkan Guard ke Model/Tabel Database masing-masing.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        'perangkat' => [
            'driver' => 'eloquent',
            'model' => Perangkat::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];