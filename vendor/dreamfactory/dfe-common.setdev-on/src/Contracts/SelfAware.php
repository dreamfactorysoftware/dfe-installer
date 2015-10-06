<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that is aware of its state of well-being
 */
interface SelfAware
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param mixed|null $target The target of the request
     *
     * @return mixed
     */
    public function status($target);
}