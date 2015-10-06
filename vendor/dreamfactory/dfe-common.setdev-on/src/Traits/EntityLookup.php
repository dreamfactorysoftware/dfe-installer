<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Database\Models\Cluster;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\Mount;
use DreamFactory\Enterprise\Database\Models\Server;
use DreamFactory\Enterprise\Database\Models\Snapshot;
use DreamFactory\Enterprise\Database\Models\User;
use Illuminate\Support\Collection;

/**
 * A trait for looking up various enterprise components
 */
trait EntityLookup
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use StaticEntityLookup;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string|int $clusterId
     *
     * @return Cluster
     */
    protected function _findCluster($clusterId)
    {
        return static::findCluster($clusterId);
    }

    protected function _findAppKey($ownerId, $ownerType)
    {
        return static::findAppKey($ownerId, $ownerType);
    }

    /**
     * @param int|string $serverId
     *
     * @return Server
     */
    protected function _findServer($serverId)
    {
        return static::findServer($serverId);
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected function _findInstance($instanceId)
    {
        return static::findInstance($instanceId);
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected function _findArchivedInstance($instanceId)
    {
        return static::findArchivedInstance($instanceId);
    }

    /**
     * Looks first in instance_t, then in instance_arch_t. If nothing found returns null.
     *
     * @param int|string $instanceId
     *
     * @return Instance|null
     */
    protected static function _locateInstance($instanceId)
    {
        return static::locateInstance($instanceId);
    }

    /**
     * @param int $userId
     *
     * @return User
     */
    protected function _findUser($userId)
    {
        return static::findUser($userId);
    }

    /**
     * @param string $snapshotId
     *
     * @return Snapshot
     */
    protected function _findSnapshot($snapshotId)
    {
        return static::findSnapshot($snapshotId);
    }

    /**
     * Returns all servers registered on $clusterId
     *
     * @param int $clusterId
     *
     * @return Collection
     */
    protected function _clusterServers($clusterId)
    {
        return static::findClusterServers($clusterId);
    }

    /**
     * Returns all clusters registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected function _serverClusters($serverId)
    {
        return static::findServerClusters($serverId);
    }

    /**
     * Returns all instances registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected function _serverInstances($serverId)
    {
        return static::findServerInstances($serverId);
    }

    /**
     * Returns all assigned roles for a user
     *
     * @param int $userId
     *
     * @return Collection
     */
    protected function _userRoles($userId)
    {
        return static::findUserRoles($userId);
    }

    /**
     * @param int $id
     * @param int $type
     *
     * @return \DreamFactory\Enterprise\Database\Models\Cluster|\DreamFactory\Enterprise\Database\Models\Instance|\DreamFactory\Enterprise\Database\Models\Server|\DreamFactory\Enterprise\Database\Models\User
     */
    protected function _locateOwner($id, $type = OwnerTypes::USER)
    {
        return static::findOwner($id, $type);
    }

    /**
     * @param string|int $mountId
     *
     * @return Mount
     */
    protected function _findMount($mountId)
    {
        return static::findMount($mountId);
    }
}