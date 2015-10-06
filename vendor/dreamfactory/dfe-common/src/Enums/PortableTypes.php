<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The types of possibly portable things in this system
 */
class PortableTypes extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string A database dump
     */
    const DATABASE = 'db';
    /**
     * @var string A storage folder archive
     */
    const STORAGE = 'storage';
    /**
     * @var string An instance definition/config file
     */
    const INSTANCE = 'instance';
}
