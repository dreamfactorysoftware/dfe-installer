<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The states in which an instance may reside
 */
class InstanceStates extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @type int
     */
    const ADMIN_REQUIRED = 0;
    /**
     * @type int
     */
    const DATA_REQUIRED = 1;
    /**
     * @type int
     */
    const INIT_REQUIRED = 2;
    /**
     * @type int
     */
    const READY = 3;
    /**
     * @type int
     */
    const SCHEMA_REQUIRED = 4;
    /**
     * @type int
     */
    const UPGRADE_REQUIRED = 5;
    /**
     * @type int
     */
    const WELCOME_REQUIRED = 6;
    /**
     * @type int Indicates that the database is in place and the schema has been created
     */
    const DATABASE_READY = 7;
}
