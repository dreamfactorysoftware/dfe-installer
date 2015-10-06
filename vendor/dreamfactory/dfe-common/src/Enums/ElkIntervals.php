<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Intervals for elk searching
 */
class ElkIntervals extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const YEAR = 'year';
    /**
     * @var string
     */
    const QUARTER = 'quarter';
    /**
     * @var string
     */
    const MONTH = 'month';
    /**
     * @var string
     */
    const WEEK = 'week';
    /**
     * @var string
     */
    const DAY = 'day';
    /**
     * @var string
     */
    const HOUR = 'hour';
    /**
     * @var string
     */
    const MINUTE = 'minute';
}
