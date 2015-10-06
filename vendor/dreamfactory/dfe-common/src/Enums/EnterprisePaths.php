<?php namespace DreamFactory\Enterprise\Common\Enums;

/**
 * Standard DSP/DFE storage paths & keys
 */
class EnterprisePaths extends EnterpriseKeys
{
    //*************************************************************************
    //* Path Construction Constants
    //*************************************************************************

    /**
     * @type string Absolute path where data is mounted
     */
    const MOUNT_POINT = '/data';
    /**
     * @type string Relative path under data mount
     */
    const STORAGE_PATH = '/storage';
    /**
     * @type string Absolute path to all storage
     */
    const DEFAULT_HOSTED_BASE_PATH = '/data/storage';
    /**
     * @type string Relative path under storage base
     */
    const PRIVATE_STORAGE_PATH = '/.private';
    /**
     * @type string Name of the applications directory relative to storage base
     */
    const APPLICATIONS_PATH = '/applications';
    /**
     * @type string Name of the plugins directory relative to storage base
     */
    const PLUGINS_PATH = '/plugins';
    /**
     * @type string Name of the config directory relative to storage and private base
     */
    const CONFIG_PATH = '/config';
    /**
     * @type string Name of the scripts directory relative to private base
     */
    const SCRIPTS_PATH = '/scripts';
    /**
     * @type string Name of the user scripts directory relative to private base
     */
    const USER_SCRIPTS_PATH = '/scripts.user';
    /**
     * @type string Name of the snapshot storage directory relative to private base
     */
    const SNAPSHOT_PATH = '/snapshots';
}
