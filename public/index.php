<?php
//******************************************************************************
//* Main
//******************************************************************************

if (!function_exists('__dfe_installer_main')) {
    /**
     * Bootstrap DFE Installer
     *
     * @return bool
     */
    function __dfe_installer_main()
    {
        //  Register The Composer Auto Loader
        require __DIR__ . '/../bootstrap/autoload.php';

        $_app = require_once __DIR__ . '/../bootstrap/app.php';
        /** @type Illuminate\Contracts\Http\Kernel $_kernel */
        $_kernel = $_app->make(Illuminate\Contracts\Http\Kernel::class);
        $_response = $_kernel->handle($_request = Illuminate\Http\Request::capture());
        $_response->send();
        $_kernel->terminate($_request, $_response);
    }
}

__dfe_installer_main();
