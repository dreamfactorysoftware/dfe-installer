<?php
namespace DreamFactory\Enterprise\Common\Packets;

use Illuminate\Http\Response;

class SuccessPacket extends BasePacket
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Creates a success packet
     *
     * @param mixed|null     $contents
     * @param int|mixed|null $httpCode
     *
     * @return array
     */
    public static function create($contents = null, $httpCode = Response::HTTP_OK)
    {
        return parent::make(true, $contents, $httpCode);
    }

}