<?php
return [
    'mailgun'  => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],
    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],
    'ses'      => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],
    'stripe'   => [
        'model'  => DreamFactory\Enterprise\Installer\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
];
