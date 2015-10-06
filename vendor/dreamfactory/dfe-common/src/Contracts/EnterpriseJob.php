<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Database\Models\EnterpriseModel;

/**
 * A contract for enterprise jobs
 */
interface EnterpriseJob
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|int $clusterId
     *
     * @return string Return the id/name of the cluster involved in this job
     */
    public function getCluster($clusterId = null);

    /**
     * @param string|int $serverId
     *
     * @return string Return the id/name of the server involved in this job
     */
    public function getServer($serverId = null);

    /**
     * @return EnterpriseModel The owner model
     */
    public function getOwner();
}