<?php
namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\EnterpriseHeaders;
use Illuminate\Http\Request;

/**
 * A trait for objects that have an interface with multiple versions
 */
trait Versioned
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Retrieve any API version passed as a query parameter or HTTP header
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function getRequestedVersion(Request $request)
    {
        static $_version;

        return $_version
            ?: $_version = $request->input('version', $request->headers->get(EnterpriseHeaders::API_VERSION));
    }
}