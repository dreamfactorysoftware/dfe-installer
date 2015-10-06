<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Default patterns for DFE things
 */
class EnterprisePatterns extends FactoryEnum
{
    //*************************************************************************
    //* Defaults
    //*************************************************************************

    /**
     * @var string
     */
    const API_ENDPOINT = '/api';
    /**
     * @var string
     */
    const AUTH_ENDPOINT = '/api/instance/credentials';
    /**
     * @var string
     */
    const OASYS_PROVIDER_ENDPOINT = '/oauth/providerCredentials';
    /**
     * @var string
     */
    const INSTANCE_CONFIG_FILE_NAME = 'instance.json';
    /**
     * @var string
     */
    const DB_CONFIG_FILE_NAME = '{instance_name}.database.config.php';
    /**
     * @type string
     */
    const DEFAULT_ENVIRONMENT_CLASS = '\\DreamFactory\\Library\\Utility\\Environment';
    /**
     * @type string
     */
    const DEFAULT_RESOLVER_CLASS = '\\DreamFactory\\Library\\Enterprise\\Storage\\Resolver';
}
