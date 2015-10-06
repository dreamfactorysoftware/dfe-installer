<?php namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait that manages the growth of collections
 * @implements \DreamFactory\Enterprise\Common\Contracts\Curated
 */
trait Curator
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Resets the current collection
     *
     * @return $this
     */
    public function reset()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->items = [];

        return $this;
    }

    /**
     * @param string     $key
     * @param mixed|null $value
     *
     * @return $this
     */
    public function add($key, $value = null)
    {
        $_value = $this->get($key, []);

        if (!is_array($_value)) {
            $_value = [$_value];
        }

        $_value[] = $value;

        return $this->put($key, $_value);
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed        $value
     *
     * @return $this
     */
    public function set($key, $value = null)
    {
        return $this->put($key, $value);
    }
}
