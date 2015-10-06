<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\RouteHashingServiceProvider;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string create(string $pathToHash, int $keepDays = 30)
 * @method static string resolve(string $hashToResolve)
 * @method static int expireFiles($fsToCheck)
 */
class RouteHashing extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return RouteHashingServiceProvider::IOC_NAME;
    }
}