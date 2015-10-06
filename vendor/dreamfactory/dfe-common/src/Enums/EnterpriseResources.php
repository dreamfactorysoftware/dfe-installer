<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Enterprise resources
 */
class EnterpriseResources extends FactoryEnum
{
    //*************************************************************************
    //* Defaults
    //*************************************************************************

    /**
     * @var int
     */
    const MOUNT_POINT = 'mount_point';
    /**
     * @var int
     */
    const INSTALL_ROOT = 'install_root';
    /**
     * @var int
     */
    const STORAGE_PATH = 'storage_path';
    /**
     * @var int
     */
    const ZONE = 'zone';
    /**
     * @var int
     */
    const PARTITION = 'partition';
}
