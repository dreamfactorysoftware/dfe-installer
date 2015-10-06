<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The classes of app keys
 */
class AppKeyClasses extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const TYPE_ENTITY = 'entity';
    /**
     * @var string
     */
    const CONSOLE = '[entity:console]';
    /**
     * @var string
     */
    const DASHBOARD = '[entity:dashboard]';
    /**
     * @var string
     */
    const APPLICATION = '[entity:application]';
    /**
     * @var string
     */
    const SERVICE = '[entity:service]';
    /**
     * @var string
     */
    const TESTING = '[testing:testing]';
    /**
     * @var string
     */
    const USER = '[entity:user]';
    /**
     * @var string
     */
    const SERVICE_USER = '[entity:service-user]';
    /**
     * @var string
     */
    const INSTANCE = '[entity:instance]';
    /**
     * @var string
     */
    const MOUNT = '[entity:mount]';
    /**
     * @var string
     */
    const SERVER = '[entity:server]';
    /**
     * @var string
     */
    const CLUSTER = '[entity:cluster]';
    /**
     * @var string
     */
    const OTHER = '[entity:other]';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a custom app ID
     *
     * @param string $type   The specific entity type
     * @param string $entity The entity classification/term. Defaults to generic "entity"
     *
     * @return string
     */
    public static function make($type, $entity = self::TYPE_ENTITY)
    {
        static $_pattern = '[{entity}:{type}]';

        if (empty($entity) || empty($type)) {
            throw new \InvalidArgumentException('Neither $entity or $type may be blank.');
        }

        return strtolower(str_replace(['{entity}', '{type}'], [$entity, $type], $_pattern));
    }

    /**
     * Given an owner type, return a key class
     *
     * @param int $ownerType The type of owner
     *
     * @return string
     */
    public static function fromOwnerType($ownerType)
    {
        return static::defines(strtoupper(OwnerTypes::nameOf($ownerType, !is_numeric($ownerType), false)), true);
    }

    /**
     * @param string $entityType Given an entity type, return the associated owner type
     *
     * @return bool
     */
    public static function mapOwnerType($entityType)
    {
        return OwnerTypes::defines(strtoupper(trim($entityType)), true);
    }
}
