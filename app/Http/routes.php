<?php
use DreamFactory\Enterprise\Installer\Installer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** @noinspection PhpUndefinedMethodInspection */
Route::get('/',
    [
        'as' => 'home',
        function () {
            $installer = new Installer();

            return view('index', $installer->getDefaults());
        },
    ]);

/** @noinspection PhpUndefinedMethodInspection */
Route::post('/',
    function (Request $request) {
        $installer = new Installer();
        $installer->setFormData($request->input());
        $installer->writeInstallerFiles();

        return view('continue', $installer->getCleanData());
    });
