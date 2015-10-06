<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Constants that correspond to HTTP headers that are recognized by DFE
 */
class EnterpriseHeaders extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @type string Specifies the API version
     */
    const API_VERSION = 'x-dreamfactory-api-version';
}
