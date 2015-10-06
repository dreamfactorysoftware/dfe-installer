<?php
namespace DreamFactory\Enterprise\Common\Packets;

use Illuminate\Http\Response;

class ErrorPacket extends BasePacket
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public static function create($contents = null, $statusCode = Response::HTTP_NOT_FOUND, $errorMessage = null)
    {
        return parent::make(false, $contents, $statusCode, $errorMessage);
    }
}