<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Constants that correspond with the keys in the app config
 */
class EnterpriseKeys extends FactoryEnum
{
    //*************************************************************************
    //* Cache/Settings Keys
    //*************************************************************************

    /**
     * @type string Installation path of the system
     */
    const INSTALL_ROOT_KEY = 'install_root';
    /**
     * @type string
     */
    const MOUNT_POINT_KEY = 'mount_point';
    /**
     * @type string
     */
    const STORAGE_PATH_KEY = 'storage_path';
    /**
     * @type string
     */
    const PRIVATE_STORAGE_PATH_KEY = 'private_storage_path';
    /**
     * @type string
     */
    const PRIVATE_PATH_KEY = 'private_path';
    /**
     * @type string
     */
    const LIBRARY_PATH_KEY = 'plugins_path';
    /**
     * @type string
     */
    const PLUGINS_PATH_KEY = 'plugins_path';
    /**
     * @type string
     */
    const APPLICATIONS_PATH_KEY = 'applications_path';
    /**
     * @type string
     */
    const SNAPSHOT_PATH_KEY = 'snapshot_path';
    /**
     * @type string
     */
    const SWAGGER_PATH_KEY = 'swagger_path';
    /**
     * @type string
     */
    const TEMPLATE_PATH_KEY = 'template_path';
    /**
     * @type string The relative path to any user-specified configuration files
     */
    const LOCAL_CONFIG_PATH_KEY = 'local_config_path';
    /**
     * @type string The relative path to the private config path
     */
    const PRIVATE_CONFIG_PATH_KEY = 'private_config_path';
    /**
     * @type string The relative path to the system config path
     */
    const SYSTEM_CONFIG_PATH_KEY = 'system_config_path';
    /**
     * @type string The relative path to any script files
     */
    const SCRIPTS_PATH_KEY = 'scripts_path';
    /**
     * @type string The relative path to any user-specified script files
     */
    const USER_SCRIPTS_PATH_KEY = 'user_scripts_path';
    /**
     * @type string The paths known by the provider
     */
    const PATHS_KEY = 'paths';
    /**
     * @type string The zone of the installation
     */
    const ZONE_KEY = 'zone';
    /**
     * @type string The partition of the storage host
     */
    const PARTITION_KEY = 'partition';

    //******************************************************************************
    //* Configuration Constants
    //******************************************************************************

    /**
     * @type string Zone name to use when testing. Set to null in production
     */
    const DEBUG_ZONE_NAME = null;
    /**
     * @type string Zone url to use when testing. Set to null in production
     */
    const DEBUG_ZONE_URL = null;
}
