<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that is aware of portability provisioners
 */
interface PortabilityAware
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Returns an array of the portability providers for this provisioner. If
     * no sub-provisioners are portable, an empty array will be returned.
     *
     * @param string $name The provisioner id. If null, the default provisioner is used.
     *
     * @return PortableData[] An array of portability services keyed by PortableTypes
     */
    public function getPortableServices($name = null);
}