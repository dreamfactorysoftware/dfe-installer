<?php namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait for things that have portable data
 */
trait HasPortableData
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array
     */
    protected $portableData;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return array
     */
    public function getPortableData()
    {
        return $this->portableData;
    }

    /**
     * @param array $portableData
     *
     * @return HasResults
     */
    public function setPortableData($portableData)
    {
        $this->portableData = $portableData;

        return $this;
    }
}