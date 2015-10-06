<?php
namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait for things that have a "result" in a collection
 */
trait HasPrivatePaths
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The private storage path for a possession of the owner
     */
    protected $privatePath = null;
    /**
     * @type string The private storage path for the user
     */
    protected $ownerPrivatePath = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|null $append An additional file or path to append to the result
     *
     * @return string
     */
    public function getPrivatePath($append = null)
    {
        return
            $this->privatePath . DIRECTORY_SEPARATOR .
            (
            $append
                ? DIRECTORY_SEPARATOR . ltrim($append, ' ' . DIRECTORY_SEPARATOR)
                : null
            );
    }

    /**
     * @param string $privatePath
     *
     * @return $this
     */
    public function setPrivatePath($privatePath)
    {
        $this->privatePath = rtrim($privatePath, ' ' . DIRECTORY_SEPARATOR);

        return $this;
    }

    /**
     * @param string|null $append An additional file or path to append to the result
     *
     * @return string
     */
    public function getOwnerPrivatePath($append = null)
    {
        return
            $this->ownerPrivatePath . DIRECTORY_SEPARATOR .
            (
            $append
                ? DIRECTORY_SEPARATOR . ltrim($append, ' ' . DIRECTORY_SEPARATOR)
                : null
            );
    }

    /**
     * @param string $ownerPrivatePath
     *
     * @return $this
     */
    public function setOwnerPrivatePath($ownerPrivatePath)
    {
        $this->ownerPrivatePath = rtrim($ownerPrivatePath, ' ' . DIRECTORY_SEPARATOR);

        return $this;
    }
}