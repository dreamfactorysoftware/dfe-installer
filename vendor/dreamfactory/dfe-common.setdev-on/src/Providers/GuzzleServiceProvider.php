<?php namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\GuzzleService;

class GuzzleServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    const IOC_NAME = 'dfe.guzzle';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function register()
    {
        $this->singleton(static::IOC_NAME,
            function ($app) {
                return new GuzzleService($app);
            });
    }
}