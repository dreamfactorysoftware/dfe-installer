<?php
//******************************************************************************
//* DFE-specific settings
//******************************************************************************
return [
    /** The output file name. Goes into storage/ */
    'output-file'       => '.env-install',
    /** The required packages, not really used any longer */
    'required-packages' => [
        'php'    => ['php5-common', 'php5'],
        'git'    => ['git', 'git-full', 'git-core'],
        'puppet' => ['puppet'],
    ],
    /** The software versions to install */
    'versions'          => [
        'kibana'        => '4.3.0',
        'logstash'      => '2.0',
        'elasticsearch' => '2.x',
    ],
];
