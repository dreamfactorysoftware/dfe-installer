<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
                'mysql_root_pwd' => 'mysql',
                'vendor_id'      => null,
                'domain'         => null,
                'mount_point'    => '/data',
                'storage_path'   => '/storage',
                'log_path'       => '/data/logs',
            ];

            return view('index', $_defaults);
        },
    ]);

Route::post('/',
    function (Request $request){
        $_data = $request->input();
        array_forget($_data, '_token');

        $_env[] = '#!/bin/sh';
        $_env[] = 'INSTALLER_FACTS=1';

        if (!empty($_data)) {
            foreach ($_data as $_key => $_value) {
                if (!empty($_value)) {
                    $_env[] = 'FACTER_' . trim(str_replace('-', '_', strtoupper($_key))) . '=' . $_value;
                }
            }

            if (file_put_contents(storage_path('.install.env'), implode(PHP_EOL, $_env))) {
                exit(0);
            }
        }

        return Redirect::home();
    });
