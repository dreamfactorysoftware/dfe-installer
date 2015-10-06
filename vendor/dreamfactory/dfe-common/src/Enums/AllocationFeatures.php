<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The types of things that can be allocated
 */
class AllocationFeatures extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var int
     */
    const CPU = 0;
    /**
     * @var int
     */
    const MEMORY = 1;
    /**
     * @var int
     */
    const DISK_STORAGE = 3;
    /**
     * @var int
     */
    const BLOB_STORAGE = 4;
    /**
     * @var int
     */
    const SQL_STORAGE = 5;
    /**
     * @var int
     */
    const DAILY_API_USAGE = 6;
    /**
     * @var int
     */
    const HOURLY_API_USAGE = 7;
    /**
     * @var int
     */
    const RUNNING_INSTANCES_PER_USER = 8;
    /**
     * @var int
     */
    const REGISTERED_USER_COUNT = 9;
    /**
     * @var int
     */
    const RUNNING_INSTANCES_PER_CLUSTER = 10;
}
