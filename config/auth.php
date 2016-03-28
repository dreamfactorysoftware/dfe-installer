<?php
//******************************************************************************
//* Authentication configuration (none for this)
//******************************************************************************
return [
    /** Defaults */
    'defaults'  => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],
    /** Guards */
    'guards'    => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver'   => 'token',
            'provider' => 'users',
        ],
    ],
    /** User Providers */
    'providers' => [
//        'users' => [
//            'driver' => 'eloquent',
//            'model'  => App\User::class,
//        ],
//        'users' => [
//            'driver' => 'database',
//            'table'  => 'users',
//        ],
    ],
    /** Resetting Passwords */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'email'    => 'auth.emails.password',
            'table'    => 'password_resets',
            'expire'   => 60,
        ],
    ],
];
