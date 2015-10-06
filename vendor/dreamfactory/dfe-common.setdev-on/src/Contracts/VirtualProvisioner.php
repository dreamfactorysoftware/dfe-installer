<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceResponse;
use DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceRequest;

/**
 * A service that provides virtual provisioning capabilities
 */
interface VirtualProvisioner
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Returns the id, config key, or short name, of this provisioner.
     *
     * @return string The id of this provisioner
     */
    public function getProvisionerId();

    /**
     * @param ProvisionServiceRequest|mixed $request
     *
     * @return ProvisionServiceResponse|mixed
     */
    public function provision($request);

    /**
     * @param ProvisionServiceRequest|mixed $request
     *
     * @return ProvisionServiceResponse|mixed
     */
    public function deprovision($request);
}