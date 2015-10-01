<?php
//******************************************************************************
//* Application Autoloader
//******************************************************************************

define('LARAVEL_START', microtime(true));

if (!function_exists('__dfe_autoload')) {
    /**
     * Bootstrap DFE
     *
     * @return bool
     */
    function __dfe_autoload()
    {
        //  Register The Composer Auto Loader
        require __DIR__ . '/../vendor/autoload.php';

        //  Laravel 5.1
        if (file_exists($_compiled = __DIR__ . '/cache/compiled.php')) {
            /** @noinspection PhpIncludeInspection */
            require $_compiled;
        }

        return true;
    }
}

return __dfe_autoload();
