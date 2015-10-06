<?php namespace DreamFactory\Enterprise\Common\Providers;

class LibraryAssetsProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string Our container name
     */
    const IOC_NAME = 'dfe-common';
    /**
     * @type string Our alias name
     */
    const ALIAS_NAME = 'Common';
    /**
     * @type string Relative path to config file
     */
    const CONFIG_NAME = 'dfe.common.php';
    /**
     * @type string Relative path of asset installation
     */
    const ASSET_PUBLISH_PATH = '/vendor/dfe-common';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        $_libBase = realpath(__DIR__ . '/../../');
        $_resourcesPath = $_libBase . '/resources';

        //  Views
        $this->loadViewsFrom($_resourcesPath . '/views', static::IOC_NAME);

        //  And assets...
        $this->publishes([$_resourcesPath . '/assets' => public_path(static::ASSET_PUBLISH_PATH)], 'public');
    }

    /** @inheritdoc */
    public function register()
    {
        //  Does nothing but is required
    }
}
