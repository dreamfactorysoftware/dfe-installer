<?php namespace DreamFactory\Enterprise\Common\Http\Middleware;

use DreamFactory\Enterprise\Common\Traits\Lumberjack;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * A delicious foundation for all sorts of middleware
 */
class BaseMiddleware implements LoggerAwareInterface, LoggerInterface
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string My ioc alias, used as default prefix
     */
    const ALIAS = false;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Lumberjack;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Psr\Log\LoggerInterface                     $logger
     */
    public function __construct(Application $app, LoggerInterface $logger = null)
    {
        $this->app = $app;

        $this->initializeLumberjack($logger, static::ALIAS);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        return $next($request);
    }

}
