<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Database\Models\Instance;

class ProvisionServiceResponse extends BaseResponse
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Instance
     */
    protected $instance;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param Instance $instance
     *
     * @return ProvisionServiceResponse
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;

        return $this;
    }
}
