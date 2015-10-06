<?php
namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Jobs\BaseJob;

/**
 * A trait for things that have results
 */
trait HasResults
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type mixed
     */
    protected $processResult = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return mixed
     */
    public function getResult()
    {
        if ($this instanceof BaseJob) {
            if (false !== ($_result = $this->trackResult($this->getJobId()))) {
                return $_result;
            }
        }

        return $this->processResult;
    }

    /**
     * @param mixed $result
     *
     * @return mixed
     */
    public function setResult($result)
    {
        $this->processResult = $result;

        return $this;
    }
}
