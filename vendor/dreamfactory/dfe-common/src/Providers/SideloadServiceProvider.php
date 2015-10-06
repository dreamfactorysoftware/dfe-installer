<?php
namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\SideloadService;

/**
 * Register the sideload service into the $app ioc @ 'sideload'
 */
class SideloadServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'sideload';
    /** @inheritdoc */
    const ALIAS_NAME = false;

    //********************************************************************************
    //* Public Methods
    //********************************************************************************

    /**
     * Boot the service
     */
    public function boot()
    {
        //  Call my autoload method
        app(static::IOC_NAME)->autoload();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->_serviceClass = 'DreamFactory\\Enterprise\\Common\\Services\\SideloadService';

        //  Register object into instance container
        $this->singleton(
            static::IOC_NAME,
            function ($app) {
                return new SideloadService($app);
            }
        );
    }
}
