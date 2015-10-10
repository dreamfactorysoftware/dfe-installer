<?php
use DreamFactory\Enterprise\Common\Providers\InspectionServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** @noinspection PhpUndefinedMethodInspection */
Route::get('/',
    [
        'as' => 'home',
        function (){
            $_defaults = [
                'user'           => 'dfadmin',
                'group'          => 'dfadmin',
                'storage_group'  => 'dfe',
                'www_user'       => 'www-data',
                'www_group'      => 'www-data',
                'admin_email'    => null,
                'admin_pwd'      => null,
                'mysql_root_pwd' => null,
                'vendor_id'      => null,
                'domain'         => null,
                'mount_point'    => '/data',
                'storage_path'   => '/storage',
                'log_path'       => '/data/logs',
                'requirements'   => [],
            ];

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
        $_data = $request->input();
        array_forget($_data, '_token');

        $_env[] = '#!/bin/sh' . PHP_EOL;
        $_env[] = 'INSTALLER_FACTS=1';

        if (!empty($_data)) {
            foreach ($_data as $_key => $_value) {
                if ($_key == 'storage-path') {
                    $_value = trim($_value, DIRECTORY_SEPARATOR);
                }

                if (!empty($_value)) {
                    $_env[] = 'export FACTER_' . trim(str_replace('-', '_', strtoupper($_key))) . '=' . $_value;
                }
            }

            file_put_contents(base_path(config('dfe.output-file')), implode(PHP_EOL, $_env) . PHP_EOL);
        }

        return Redirect::home();
    });
