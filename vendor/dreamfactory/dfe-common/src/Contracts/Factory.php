<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes a service that can create things
 */
interface Factory
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new thing
     *
     * @param string $abstract The abstract name of the thing to create
     * @param array  $data     Any data needed to create the thing
     *
     * @return mixed
     */
    public function make($abstract, $data = []);

}