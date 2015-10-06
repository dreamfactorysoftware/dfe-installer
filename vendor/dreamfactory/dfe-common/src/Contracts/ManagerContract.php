<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes an object that manages other things
 */
interface ManagerContract
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $tag    The identifier of this thing
     * @param mixed  $thing  The thing to manage
     * @param bool   $shared True if this is a singleton, otherwise false.
     *
     * @return ManagerContract
     */
    public function manage($tag, $thing, $shared = false);

    /**
     * @param string $tag The tag to remove from the manager
     *
     * @return ManagerContract
     */
    public function unmanage($tag);

    /**
     * Returns the thing assigned to $tag.
     *
     * @param string $tag
     *
     * @return mixed
     * @throws \InvalidArgumentException when nothing is managed under $tag
     */
    public function resolve($tag);
}