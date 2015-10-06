<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that is aware of private paths
 */
interface PrivatePathAware
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string $append
     *
     * @return string
     */
    public function getPrivatePath($append = null);

    /**
     * @param string $append
     *
     * @return string
     */
    public function getOwnerPrivatePath($append = null);
}