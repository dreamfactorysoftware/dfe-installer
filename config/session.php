<?php
return [
    'driver'          => env('SESSION_DRIVER', 'file'),
    'lifetime'        => 120,
    'expire_on_close' => false,
    'encrypt'         => false,
    'files'           => storage_path('framework/sessions'),
    'connection'      => null,
    'table'           => 'sessions',
    'lottery'         => [2, 100],
    'cookie'          => 'laravel_session',
    'path'            => '/',
    'domain'          => null,
    'secure'          => false,
];
