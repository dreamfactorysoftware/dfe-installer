<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Tools to help a curator
 */
interface Curated
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string     $key
     * @param mixed|null $value
     *
     * @return $this
     */
    public function add($key, $value = null);

    /**
     * Resets the current collection
     *
     * @return $this
     */
    public function reset();

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed        $value
     *
     * @return $this
     */
    public function set($key, $value = null);
}