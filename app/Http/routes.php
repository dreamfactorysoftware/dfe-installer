<?php
use DreamFactory\Enterprise\Common\Providers\InspectionServiceProvider;
use DreamFactory\Library\Utility\Disk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Psy\Util\Json;

/** @noinspection PhpUndefinedMethodInspection */
Route::get('/',
    [
        'as' => 'home',
        function (){
            $_outputFile = config('dfe.output-file', '.env-install');
            $_jsonFile = base_path($_outputFile . 'json');

            $_defaults = [
                'user'           => 'dfadmin',
                'group'          => 'dfadmin',
                'storage_group'  => 'dfadmin',
                'www_user'       => 'www-data',
                'www_group'      => 'www-data',
                'admin_email'    => null,
                'admin_pwd'      => null,
                'mysql_root_pwd' => null,
                'vendor_id'      => 'dfe',
                'domain'         => null,
                'mount_point'    => '/data',
                'storage_path'   => '/storage',
                'log_path'       => '/data/logs',
                'requirements'   => [],
            ];

            if (file_exists($_jsonFile)) {
                logger('Found existing values file "' . $_jsonFile . '"');
                
                try {
                    $_json = \DreamFactory\Library\Utility\JsonFile::decodeFile($_jsonFile);
                    $_defaults = array_merge($_defaults, $_json);
                    \Log::debug('Prior values read from "' . $_jsonFile . '": ' . print_r($_json, true));
                } catch (\Exception $_ex) {
                    //  Bogus JSON, just ignore
                }
            }

            $_required = config('dfe.required-packages', []);
            $_service = \App::make(InspectionServiceProvider::IOC_NAME);

            foreach ($_required as $_name => $_packages) {
                if (!is_array($_packages)) {
                    $_packages = [$_packages];
                }

                $_hasPackage = false;

                foreach ($_packages as $_package) {
                    if (false !== ($_hasPackage = $_service->hasPackage($_package))) {
                        break;
                    }
                }

                $_defaults['requirements'][$_name] = [
                    'name'        => $_name,
                    'has-package' => $_hasPackage,
                    'status'      => $_hasPackage ? 'text-success' : 'text-danger',
                ];
            }

            return view('index', $_defaults);
        },
    ]);

/** @noinspection PhpUndefinedMethodInspection */
Route::post('/',
    function (Request $request){
        $_rawData = [];
        $_mountPoint = $storagePath = null;

        $_formData = $request->input();

        if (empty($_formData) || count($_formData) < 5) {
            \Log::error('Invalid number of post entries: ' . print_r($_formData, true));
            Redirect::home();
        }

        array_forget($_formData, '_token');

        $_env = ['#!/bin/sh' . PHP_EOL, 'INSTALLER_FACTS=1'];

        if (!empty($_formData)) {
            foreach ($_formData as $_key => $_value) {
                $_value = trim($_value);

                //  Clean up slashes on values
                switch ($_key) {
                    case 'storage-path':
                        $_storagePath = $_value = rtrim($_value, DIRECTORY_SEPARATOR);
                        break;

                    case 'mount-point':
                        $_mountPoint = $_value = rtrim($_value, DIRECTORY_SEPARATOR);
                        break;
                }

                //  Keep a pristine copy
                $_rawData[$_key] = $_value;

                //  Dump non-empties into the source file
                if (!empty($_value)) {
                    $_env[] = 'export FACTER_' . trim(str_replace('-', '_', strtoupper($_key))) . '=' . $_value;
                }
            }

            //  If set have a storage and mount, construct a storage path
            if (!empty($_storagePath) && !empty($_mountPoint)) {
                $_env[] = 'export FACTER_STORAGE_MOUNT_POINT=' . Disk::path([$_mountPoint, $_storagePath]);
            }

            //  Write out the .env-install
            file_put_contents(base_path(config('dfe.output-file')), implode(PHP_EOL, $_env) . PHP_EOL);

            //  Write out a JSON version of the .env-install
            file_put_contents(base_path(config('dfe.output-file')) . '.json',
                Json::encode($_rawData, JSON_PRETTY_PRINT));
        }

        return view('continue', $_formData);
    });
