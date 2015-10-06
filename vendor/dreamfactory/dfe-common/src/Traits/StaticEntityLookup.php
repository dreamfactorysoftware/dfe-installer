<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\ServerTypes;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Database\Models\AppKey;
use DreamFactory\Enterprise\Database\Models\Cluster;
use DreamFactory\Enterprise\Database\Models\ClusterServer;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\InstanceArchive;
use DreamFactory\Enterprise\Database\Models\InstanceServer;
use DreamFactory\Enterprise\Database\Models\Mount;
use DreamFactory\Enterprise\Database\Models\Server;
use DreamFactory\Enterprise\Database\Models\ServiceUser;
use DreamFactory\Enterprise\Database\Models\Snapshot;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Database\Models\UserRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * A trait for looking up various enterprise components statically
 */
trait StaticEntityLookup
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     *
     * @param int $ownerId
     * @param int $ownerType
     *
     * @return AppKey
     */
    protected static function findAppKey($ownerId, $ownerType)
    {
        return AppKey::mine($ownerId, $ownerType);
    }

    /**
     * @param int $userId
     *
     * @return User
     */
    protected static function findUser($userId)
    {
        return User::findOrFail($userId);
    }

    /**
     * @param int $serviceUserId
     *
     * @return User
     */
    protected static function findServiceUser($serviceUserId)
    {
        return ServiceUser::findOrFail($serviceUserId);
    }

    /**
     * @param string|int $clusterId
     *
     * @return Cluster
     */
    protected static function findCluster($clusterId)
    {
        return Cluster::byNameOrId($clusterId)->firstOrFail();
    }

    /**
     * @param int|string $serverId
     *
     * @return Server
     */
    protected static function findServer($serverId)
    {
        return Server::byNameOrId($serverId)->firstOrFail();
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected static function findInstance($instanceId)
    {
        return Instance::with(['user', 'guest'])->byNameOrId($instanceId)->firstOrFail();
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected static function findArchivedInstance($instanceId)
    {
        return InstanceArchive::with(['user', 'guest'])->byNameOrId($instanceId)->firstOrFail();
    }

    /**
     * Looks first in instance_t, then in instance_arch_t. If nothing found returns null.
     *
     * @param int|string $instanceId
     *
     * @return Instance|null
     */
    protected static function locateInstance($instanceId)
    {
        try {
            return static::findInstance($instanceId);
        } catch (ModelNotFoundException $_ex) {
            try {
                return static::findArchivedInstance($instanceId);
            } catch (ModelNotFoundException $_ex) {
                return null;
            }
        }
    }

    /**
     * @param string|int $snapshotId
     *
     * @return Snapshot|\Illuminate\Database\Eloquent\Model
     */
    protected static function findSnapshot($snapshotId)
    {
        return Snapshot::bySnapshotId($snapshotId)->with(['user', 'routeHash'])->firstOrFail();
    }

    /**
     * @param int|string $mountId
     *
     * @return Instance
     */
    protected static function findMount($mountId)
    {
        return Mount::byNameOrId($mountId)->firstOrFail();
    }

    /**
     * Returns all servers registered on $clusterId
     *
     * @param int $clusterId
     *
     * @return array
     */
    protected static function findClusterServers($clusterId)
    {
        $_cluster = static::findCluster($clusterId);
        $_rows = $_cluster->assignedServers();

        //  Organize by type
        $_response = [
            ServerTypes::APP => [],
            ServerTypes::DB  => [],
            ServerTypes::WEB => [],
        ];

        /** @type Server $_server */
        foreach ($_rows as $_assignment) {
            if (null !== ($_server = $_assignment->server)) {
                $_response[$_server->server_type_id][$_server->server_id_text] = $_server;
            }
        }

        return $_response;
    }

    /**
     * Returns all instances registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected static function findServerInstances($serverId)
    {
        return InstanceServer::join('instance_t', 'id', '=', 'instance_id')
            ->where('server_id', '=', $serverId)
            ->orderBy('instance_t.instance_id_text')
            ->get(['instance_t.*']);
    }

    /**
     * Returns all assigned roles for a user
     *
     * @param int $userId
     *
     * @return Collection
     */
    protected static function findUserRoles($userId)
    {
        return UserRole::join('role_t', 'id', '=', 'role_id')
            ->where('user_id', '=', $userId)
            ->orderBy('role_t.role_name_text')
            ->get(['role_t.*']);
    }

    /**
     * Returns an collection of clusters to which $serverId is assigned
     *
     * @param string|int $serverId
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    protected static function findServerClusters($serverId)
    {
        return ClusterServer::with(['server', 'cluster'])->where('server_id', '=', $serverId)->get();
    }

    /**
     * @param int $id
     * @param int $type
     *
     * @return \DreamFactory\Enterprise\Database\Models\Cluster|\DreamFactory\Enterprise\Database\Models\Instance|\DreamFactory\Enterprise\Database\Models\Server|\DreamFactory\Enterprise\Database\Models\User
     */
    protected static function findOwner($id, $type = OwnerTypes::USER)
    {
        try {
            $_owner = OwnerTypes::getOwner($id, $type);
        } catch (\Exception $_ex) {
            is_string($id) && $_owner = User::byEmail($id)->first();
        }
        finally {
            if (empty($_owner)) {
                throw new ModelNotFoundException('The owner-id "' . $id . '" could not be found.');
            }
        }

        return $_owner;
    }
}