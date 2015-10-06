<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The operational states in which a platform may reside
 */
class OperationalStates extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @type int Set by the DFE system when a schema exists without an instance record
     */
    const NO_INSTANCE_RECORD = -2;
    /**
     * @type int Set by the DFE system when needed before it's been set
     */
    const UNKNOWN = -1;
    /**
     * @var int The default for newly provisioned DSPs
     */
    const NOT_ACTIVATED = 0;
    /**
     * @var int
     */
    const ACTIVATED = 1;
    /**
     * @var int
     */
    const LOCKED = 2;
    /**
     * @var int
     */
    const MAINTENANCE = 3;
    /**
     * @var int
     */
    const TOS_VIOLATION = 4;
}
