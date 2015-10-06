<?php
namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait for things that have a "result" in a collection
 */
trait HasCollectionResults
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return mixed
     */
    public function getResult()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->get('result');
    }

    /**
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->put('result', $result);

        return $this;
    }
}