<?php namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\DataShaperService;

/**
 * Register the data shaper service into the $app
 */
class DataShaperServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'dfe.data-shaper';
    /** @inheritdoc */
    const ALIAS_NAME = 'DataShaper';

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
                return new DataShaperService($app);
            });
    }
}
