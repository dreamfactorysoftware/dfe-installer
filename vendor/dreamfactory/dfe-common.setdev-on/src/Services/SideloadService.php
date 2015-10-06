<?php
namespace DreamFactory\Enterprise\Common\Services;

use Illuminate\Foundation\Application;

/**
 * DFE Services Sideloader
 *
 * Any services listed in the auto-register key will be automatically registered with the $app.
 *
 * The format is (in services.json):
 *
 * 'services' => [
 *
 *      ...
 *      'ioc-name' => 'namespaced\class\name',
 *      ...
 * ]
 *
 * where:
 *
 *  ioc-name        is the index into the $app's IoC container where this will be placed (i.e. $app['snapshot'])
 *  class-name      is the fully namespaced class name of the service to auto register
 *
 * The ioc-name will also be used as the configuration section name. For instance, if your ioc-name is "snapshot", the config for the service must be
 * placed under "services.snapshot"
 */
class SideloadService extends BaseService
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param array $providers
     *
     * @return int
     */
    public function autoload($providers = [])
    {
        /** @type Application $_app */
        $_app = @app();
        $_count = 0;

        if ($_app && null !== ($_services = array_merge(config('services.auto-register', []), $providers))) {
            foreach ($_services as $_tag => $_service) {
                $_app->register(new $_service($_app), config('services.' . $_tag, []));

                $_count++;
            }
        }

        return $_count;
    }
}