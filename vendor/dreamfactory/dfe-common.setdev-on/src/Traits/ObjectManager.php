<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Contracts\ManagerContract;

/**
 * A trait that adds object management to a class
 *
 * @implements \DreamFactory\Enterprise\Common\Contracts\ManagerContract;
 */
trait ObjectManager
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array The things I'm managing
     */
    private $_things = [];
    /**
     * @type \Closure[] User-defined custom extensions
     */
    private $_extensions = [];

    //********************************************************************************
    //* Methods
    //********************************************************************************

    /**
     * @param string $tag       The identifier of this thing
     * @param object $thing     The thing to manage
     * @param bool   $overwrite If $tag already exists, and $overwrite is FALSE, an exception will be thrown.
     *
     * @return ManagerContract
     */
    public function manage($tag, $thing, $overwrite = false)
    {
        if (false === $overwrite && array_key_exists($tag, $this->_things)) {
            throw new \InvalidArgumentException('Item at "' . $tag . '" already exists. Overwrite not allowed.');
        }

        $this->_things[$tag] = $thing;

        return $this;
    }

    /**
     * @param string $tag The tag to remove from the manager
     *
     * @return ManagerContract
     */
    public function unmanage($tag)
    {
        if (array_key_exists($tag, $this->_things)) {
            $this->_things[$tag] = null;
            unset($this->_things[$tag]);
        }

        return $this;
    }

    /**
     * Returns the thing assigned to $tag.
     *
     * @param string $tag
     *
     * @return mixed
     * @throws \InvalidArgumentException when nothing is managed under $tag
     */
    public function resolve($tag)
    {
        if (isset($this->_things[$tag])) {
            return $this->_things[$tag];
        }

        throw new \InvalidArgumentException('There is nothing assigned to "' . $tag . '".');
    }

    /**
     * Register a custom extension for a tag
     *
     * @param  string   $tag
     * @param  \Closure $callback
     *
     * @return $this
     */
    public function extend($tag, \Closure $callback)
    {
        $this->_extensions[$tag] = $callback;

        return $this;
    }

    /**
     * Call a custom extension
     *
     * @param  string $tag
     * @param  array  $config
     *
     * @return $this
     */
    protected function _callExtension($tag, array $config = [])
    {
        if (!isset($this->_things[$tag])) {
            throw new \InvalidArgumentException('There is no extension defined for "' . $tag . '".');
        }

        return $this->_extensions[$tag]($config);
    }

    /**
     * @return \IteratorIterator
     */
    public function getIterator()
    {
        return new \IteratorIterator(new \ArrayObject($this->_things));
    }

}
