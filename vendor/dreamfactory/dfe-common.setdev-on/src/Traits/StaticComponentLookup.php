<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\ServerTypes;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Database\Models\AppKey;
use DreamFactory\Enterprise\Database\Models\Cluster;
use DreamFactory\Enterprise\Database\Models\EnterpriseModel;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\InstanceServer;
use DreamFactory\Enterprise\Database\Models\Mount;
use DreamFactory\Enterprise\Database\Models\Server;
use DreamFactory\Enterprise\Database\Models\ServiceUser;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Database\Models\UserRole;
use Illuminate\Support\Collection;

/**
 * A trait for looking up various enterprise components statically
 */
trait StaticComponentLookup
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string|int $clusterId
     *
     * @return Cluster
     */
    protected static function _lookupCluster($clusterId)
    {
        return Cluster::byNameOrId($clusterId)->firstOrFail();
    }

    /**
     *
     * @param int $ownerId
     * @param int $ownerType
     *
     * @return AppKey
     */
    protected static function _lookupAppKey($ownerId, $ownerType)
    {
        return AppKey::mine($ownerId, $ownerType);
    }

    /**
     * @param int|string $serverId
     *
     * @return Server
     */
    protected static function _lookupServer($serverId)
    {
        return Server::byNameOrId($serverId)->firstOrFail();
    }

    /**
     * @param int|string $instanceId
     *
     * @return Instance
     */
    protected static function _lookupInstance($instanceId)
    {
        return Instance::byNameOrId($instanceId)->firstOrFail();
    }

    /**
     * @param int|string $mountId
     *
     * @return Instance
     */
    protected static function _lookupMount($mountId)
    {
        return Mount::byNameOrId($mountId)->firstOrFail();
    }

    /**
     * @param int $userId
     *
     * @return User
     */
    protected static function _lookupUser($userId)
    {
        return User::findOrFail($userId);
    }

    /**
     * @param int $serviceUserId
     *
     * @return User
     */
    protected static function _lookupServiceUser($serviceUserId)
    {
        return ServiceUser::findOrFail($serviceUserId);
    }

    /**
     * Returns all servers registered on $clusterId
     *
     * @param Cluster|int $clusterId
     *
     * @return array
     */
    protected static function _lookupClusterServers($clusterId)
    {
        $_cluster = ($clusterId instanceof Cluster) ? $clusterId : static::_lookupCluster($clusterId);

        $_rows = \DB::select(<<<MYSQL
SELECT
    s.id,
    s.server_id_text,
    s.server_type_id,
    csa.cluster_id
FROM
    cluster_server_asgn_t csa
JOIN server_t s ON
    s.id = csa.server_id
WHERE
    csa.cluster_id = :cluster_id
MYSQL
            ,
            [':cluster_id' => $_cluster->id]);

        //  Organize by type
        $_servers = ['cluster' => $_cluster];

        foreach (ServerTypes::getDefinedConstants() as $_name => $_value) {
            $_servers[$_value] = [
                '.id'   => null,
                '.ids'  => [],
                '.name' => $_name,
                'data'  => [],
            ];
        }

        /**
         * @type Server $_server
         */
        foreach ($_rows as $_server) {
            if (!isset($_servers[$_server->server_type_id])) {
                continue;
            }

            $_servers[$_server->server_type_id]['data'][$_server->server_id_text] = (array)$_server;
            $_servers[$_server->server_type_id]['.ids'][] = $_server->id;
        }

        //  Set the single id for quick lookups
        foreach ($_servers as $_type => $_group) {
            if (null !== array_get($_group, '.id')) {
                continue;
            }

            if (null !== ($_list = array_get($_group, '.ids'))) {
                if (!empty($_list) && is_array($_list)) {
                    $_servers[$_type]['.id'] = $_list[0];
                    continue;
                }
            }

            if (null !== ($_list = array_get($_group, 'data'))) {
                if (!empty($_list) && is_array($_list)) {
                    foreach ($_list as $_item) {
                        if (isset($_item['id'])) {
                            $_servers[$_type['.id']] = $_item['id'];
                            continue;
                        }
                    }
                }
            }
        }

        return $_servers;
    }

    /**
     * Returns all instances registered on $serverId
     *
     * @param int $serverId
     *
     * @return Collection
     */
    protected static function _lookupServerInstances($serverId)
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
    protected static function _lookupUserRoles($userId)
    {
        return UserRole::join('role_t', 'id', '=', 'role_id')
            ->where('user_id', '=', $userId)
            ->orderBy('role_t.role_name_text')
            ->get(['role_t.*']);
    }

    /**
     * Given an enterprise model, return the OwnerType associated with the entity
     *
     * @param \DreamFactory\Enterprise\Database\Models\EnterpriseModel $entity
     *
     * @return int|null The OwnerTypes constant value or null if not found
     */
    protected static function _getOwnerTypeFromEntity(EnterpriseModel $entity)
    {
        if ($entity instanceof User) {
            return OwnerTypes::USER;
        } elseif ($entity instanceof ServiceUser) {
            return OwnerTypes::SERVICE_USER;
        } elseif ($entity instanceof Instance) {
            return OwnerTypes::INSTANCE;
        } else if ($entity instanceof Server) {
            return OwnerTypes::SERVER;
        } else if ($entity instanceof Cluster) {
            return OwnerTypes::CLUSTER;
        } else if ($entity instanceof Mount) {
            return OwnerTypes::MOUNT;
        }

        return null;
    }
}