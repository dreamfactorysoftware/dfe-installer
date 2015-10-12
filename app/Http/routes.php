<?php
use DreamFactory\Enterprise\Common\Providers\InspectionServiceProvider;
use DreamFactory\Library\Utility\Disk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Psy\Util\Json;

/** @noinspection PhpUndefinedMethodInspection */
Route::get('/', [
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
Route::post('/', function (Request $request){
    $_data = $request->input();

    if (empty($_data) || count($_data) < 5) {
        Redirect::home();
    }

    array_forget($_data, '_token');

    $_env[] = '#!/bin/sh' . PHP_EOL;
    $_env[] = 'INSTALLER_FACTS=1';
    $_data = [];
    $_mountPoint = $storagePath = null;

    if (!empty($_data)) {
        foreach ($_data as $_key => $_value) {
            switch ($_key) {
                case 'storage-path':
                case 'mount-point':
                    $_mountPoint = rtrim($_value, DIRECTORY_SEPARATOR);
                    break;
            }

            if ($_key == 'storage-path') {
                $_storagePath = $_value = trim($_value, DIRECTORY_SEPARATOR);
            }

            if (!empty($_value)) {
                $_env[] = 'export FACTER_' . trim(str_replace('-', '_', strtoupper($_key))) . '=' . $_value;
            }

            $_data[$_key] = $_value;
        }

        if (!empty($_storagePath) && !empty($_mountPoint)) {
            $_env[] = 'export FACTER_STORAGE_MOUNT_POINT=' . Disk::path([$_mountPoint, $_storagePath], true);
        }

        //  Write out the .env-install and the .env-install.json version
        file_put_contents(base_path(config('dfe.output-file')), implode(PHP_EOL, $_env) . PHP_EOL);
        file_put_contents(base_path(config('dfe.output-file') . '.json'), Json::encode($_data, JSON_PRETTY_PRINT));
    }

    return view('continue', $_data);
});
