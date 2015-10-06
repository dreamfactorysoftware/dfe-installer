<?php namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Contracts\EnterpriseJob;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;

/**
 * A base class for all DFE non-instance "job" type commands (non-console)
 */
abstract class BaseEnterpriseJob extends BaseJob implements EnterpriseJob
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use EntityLookup;

    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string|bool The queue upon which to push myself. Set to false to not use queuing
     */
    const JOB_QUEUE = 'enterprise';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string|int The id of the cluster
     */
    protected $clusterId;
    /**
     * @type string|int The id of the web server
     */
    protected $serverId;
    /**
     * @type int The owner ID
     */
    protected $ownerId;
    /**
     * @type int The owner type
     */
    protected $ownerType;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|int|null $clusterId
     * @param string|int|null $serverId
     * @param string|null     $tag Optional string to have added to the job id
     */
    public function __construct($clusterId = null, $serverId = null, $tag = null)
    {
        parent::__construct($tag);

        $this->setClusterId($clusterId ?: config('provisioning.default-cluster-id'));
        $this->setServerId($serverId ?: config('provisioning.default-db-server-id'));
    }

    /**
     * @return mixed
     */
    public function getClusterId()
    {
        return $this->clusterId;
    }

    /**
     * @param mixed $clusterId
     *
     * @return $this
     */
    public function setClusterId($clusterId)
    {
        $_cluster = $this->_findCluster($clusterId);
        $this->clusterId = $_cluster->cluster_id_text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServerId()
    {
        return $this->serverId;
    }

    /**
     * @param mixed $serverId
     *
     * @return $this
     */
    public function setServerId($serverId)
    {
        $_server = $this->_findServer($serverId);
        $this->serverId = $_server->server_id_text;

        return $this;
    }

    /**
     * @param int $ownerId
     * @param int $ownerType
     *
     * @return $this
     */
    public function setOwner($ownerId, $ownerType)
    {
        $_owner = $this->_locateOwner($ownerId, $ownerType);

        $this->ownerId = $_owner->id;
        $this->ownerType = $_owner->owner_type_nbr;

        return $this;
    }

    /**
     * Retrieve the owner row
     *
     * @return \DreamFactory\Enterprise\Database\Models\Cluster|\DreamFactory\Enterprise\Database\Models\Instance|\DreamFactory\Enterprise\Database\Models\Server|\DreamFactory\Enterprise\Database\Models\User|null
     */
    public function getOwner()
    {
        if ($this->ownerId) {
            if (null === $this->ownerType) {
                $this->ownerType = OwnerTypes::USER;
            }

            return $this->_locateOwner($this->ownerId, $this->ownerType);
        }

        return null;
    }

    /** @inheritdoc */
    public function getCluster($clusterId = null)
    {
        return $this->_findCluster($clusterId ?: $this->clusterId);
    }

    /** @inheritdoc */
    public function getServer($serverId = null)
    {
        return $this->_findServer($serverId ?: $this->serverId);
    }

}
