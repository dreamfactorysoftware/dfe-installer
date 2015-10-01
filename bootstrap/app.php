<?php
//******************************************************************************
//* Application Bootstrap
//******************************************************************************

if (!function_exists('__dfe_bootstrap')) {
    /**
     * @return \Illuminate\Foundation\Application
     */
    function __dfe_bootstrap()
    {
        //  Create the app
        $_app = new Illuminate\Foundation\Application(realpath(__DIR__ . '/../'));

        //  Bind our default services
        $_app->singleton('Illuminate\Contracts\Http\Kernel', 'DreamFactory\Enterprise\Installer\Http\Kernel');
        $_app->singleton('Illuminate\Contracts\Console\Kernel', 'DreamFactory\Enterprise\Installer\Console\Kernel');
        $_app->singleton('Illuminate\Contracts\Debug\ExceptionHandler',
            'DreamFactory\Enterprise\Installer\Exceptions\Handler');

        //  Return the app
        return $_app;
    }
}

return __dfe_bootstrap();
