<?php
namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\ElkService;

/**
 * Gets data from the ELK system
 */
class ElkServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const IOC_NAME = 'dfe.elk';

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->singleton(
            static::IOC_NAME,
            function ($app) {
                return new ElkService($app);
            }
        );
    }
}
