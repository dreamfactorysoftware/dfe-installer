<?php namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\PacketService;

/**
 * Register the packet service as a provider with Laravel.
 *
 * To use the "Packet" facade for this provider, you need to add the service provider to
 * your the providers array in your app/config/app.php file:
 *
 *  'providers' => array(
 *
 *      ... Other Providers Above ...
 *      'DreamFactory\Library\Fabric\Api\Common\Providers\PacketServiceProvider',
 *
 *  ),
 */
class PacketServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'packet';

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
        $this->singleton(static::IOC_NAME,
            function ($app){
                return new PacketService($app);
            });
    }

}
