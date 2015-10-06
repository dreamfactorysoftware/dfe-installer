<?php
namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\RouteHashingService;

/**
 * Register the route hashing service as a Laravel provider
 *
 * To use the "RouteHash" facade for this provider, add the service provider to
 * your the "providers" array in your config/app.php file:
 *
 *  'providers' => array(
 *
 *      ... Other Providers Above ...
 *      'DreamFactory\Enterprise\Services\Providers\RouteHashingServiceProvider',
 *
 *  ),
 */
class RouteHashingServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'route-hashing';
    /** @inheritdoc */
    const ALIAS_NAME = 'RouteHashing';

    //********************************************************************************
    //* Public Methods
    //********************************************************************************

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //  Register object into instance container
        $this->app->singleton(
            static::IOC_NAME,
            function ($app) {
                return new RouteHashingService($app);
            }
        );
    }

}
