<?php namespace DreamFactory\Enterprise\Common\Http\Middleware;

use Illuminate\Http\Request;

/**
 * Simple middleware that logs api requests to the log
 */
class ApiLogger extends BaseMiddleware
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string My alias in the ioc and for logging
     */
    const ALIAS = 'log.dfe-ops-api';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Log all api requests
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $this->debug($request->getMethod() . ': ' . $request->getPathInfo());

        return parent::handle($request, $next);
    }

}
