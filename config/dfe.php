<?php
//******************************************************************************
//* DFE-specific settings
//******************************************************************************
return [
    'output-file'       => '.env-install',
    'required-packages' => [
        'php'    => ['php5-common', 'php5'],
        'git'    => ['git', 'git-full', 'git-core'],
        'puppet' => ['puppet'],
    ],
];
