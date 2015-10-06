<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Common\Provisioners\BaseResponse;

/**
 * Something that looks like it can provision resources
 */
interface ResourceProvisioner extends VirtualProvisioner
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Returns the overall response to the request once handled
     *
     * @return BaseResponse
     */
    public function getResponse();
}