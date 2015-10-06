<?php
namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Traits\Lumberjack;
use Illuminate\Contracts\Foundation\Application;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * A base class for services that are logger-aware
 */
class BaseService implements LoggerInterface, LoggerAwareInterface
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type int Our current API version
     */
    const API_VERSION = 1;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Lumberjack;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Application No underscore so it matches ServiceProvider class...
     */
    protected $app;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct($app = null)
    {
        $this->app = $app;
        $this->initializeLumberjack(\Log::getMonolog());

        $this->boot();
    }

    /**
     * Perform any service initialization
     */
    public function boot()
    {
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

}
