<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The types of servers that can join a cluster.
 *
 * Important note!
 *
 * These values correspond with the values stored in the server_type_t table. If either should change, change
 * the remaining one accordingly.
 */
class ServerTypes extends FactoryEnum
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type int Database server
     */
    const DB = 1;
    /**
     * @type int Web server
     */
    const WEB = 2;
    /**
     * @type int Application server
     */
    const APP = 3;
}
