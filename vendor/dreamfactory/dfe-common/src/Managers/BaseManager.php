<?php
namespace DreamFactory\Enterprise\Common\Managers;

use DreamFactory\Enterprise\Common\Contracts\ManagerContract;
use DreamFactory\Enterprise\Common\Services\BaseService;
use DreamFactory\Enterprise\Common\Traits\ObjectManager;

/**
 * A base class for DFE service providers
 */
abstract class BaseManager extends BaseService implements ManagerContract
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use ObjectManager;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Call forwarder
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        list($_tag, $_args) = $this->_filterTag($arguments);

        return call_user_func_array([$this->resolve($_tag), $method], $_args);
    }

    /**
     * Pulls the $tag out of the argument stack and returns a new array of both
     *
     * @param array $arguments
     *
     * @return array [$tag, $newArguments]
     */
    protected function _filterTag(array $arguments)
    {
        if (empty($arguments)) {
            throw new \LogicException('You must have at least one argument.');
        }

        $_tag = array_shift($arguments);

        if (!is_string($_tag)) {
            throw new \InvalidArgumentException('First argument must be a string');
        }

        return [$_tag, $arguments];
    }

}
