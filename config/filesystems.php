<?php
return [
    'default' => 'local',
    'cloud'   => 's3',
    'disks'   => [
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],
    ],
];
