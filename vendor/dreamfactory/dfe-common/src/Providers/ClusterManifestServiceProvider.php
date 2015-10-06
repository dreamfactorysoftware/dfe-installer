<?php namespace DreamFactory\Enterprise\Common\Providers;

use DreamFactory\Enterprise\Common\Services\ClusterManifestService;
use Illuminate\Contracts\Foundation\Application;

/**
 * Register the env generation service
 *
 * This service creates a ".dfe.cluster.json" file suitable for use with deployed instances in a DFE installation
 * your the "providers" array in your config/app.php file:
 */
class ClusterManifestServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'dfe.services.cluster-manifest';

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
        $this->singleton(
            static::IOC_NAME,
            function (Application $app) {
                return new ClusterManifestService($app);
            }
        );
    }
}
