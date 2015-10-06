<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * The contract for a provisioner who is aware of offerings
 */
interface OfferingsAware
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Returns the offerings available for this provisioner
     *
     * @param string $provisionerId The provisioner ID
     *
     * @return \DreamFactory\Enterprise\Common\Contracts\Offering[]
     */
    public function getOfferings($provisionerId = null);
}