<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\PacketServiceProvider;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\Response;

/**
 * Packet
 *
 * @method static array success(mixed $contents = null, int $code = Response::HTTP_OK);
 * @method static array failure(mixed $contents = null, int $code = Response::HTTP_OK, mixed $message = null);
 */
class Packet extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PacketServiceProvider::IOC_NAME;
    }

}