<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Common\Provisioners\PortableServiceRequest;

/**
 * Something that is portable
 */
interface PortableData
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Imports something from somewhere
     *
     * @param mixed|PortableServiceRequest $request A generic portability request
     *
     * @return mixed
     */
    public function import($request);

    /**
     * Exports something to somewhere
     *
     * @param mixed|PortableServiceRequest $request A generic portability request
     *
     * @return mixed
     */
    public function export($request);
}
