<?php namespace DreamFactory\Enterprise\Common\Contracts;

use Illuminate\Http\Request;

/**
 * The contract for an object with the Versioned trait
 */
interface IsVersioned
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Retrieve the requested version, if any
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return string
     */
    public function getRequestedVersion(Request $request);
}