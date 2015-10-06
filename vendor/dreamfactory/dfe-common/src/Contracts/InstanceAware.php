<?php namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Database\Models\Instance;

/**
 * Something that is aware of an instance
 */
interface InstanceAware
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return Instance
     */
    public function getInstance();

}