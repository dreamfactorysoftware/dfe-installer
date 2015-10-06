<?php namespace DreamFactory\Enterprise\Common\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;

/**
 * Container for an array of data to be written out to disk, database or used in memory
 */
class Canister implements Arrayable, Jsonable, \JsonSerializable
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Collection The contents of the canister
     */
    protected $contents;
    /**
     * @type array Optional array of allowed keys for this canister
     */
    protected $allowedKeys = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param mixed $contents The contents to write to the file if being created
     */
    public function __construct($contents = [])
    {
        if (!empty($contents)) {
            $this->reset($contents);
        } else {
            $this->contents = new Collection();
        }
    }

    /**
     * Merges existing data and $contents into new collection
     *
     * @param array $dataToMerge The data to merge
     *
     * @return $this
     */
    protected function merge(array $dataToMerge = [])
    {
        empty($dataToMerge) && ($dataToMerge = []);

        $this->contents = $this->contents->merge($this->removeDisallowedKeys($dataToMerge));

        return $this;
    }

    /**
     * Completely resets the contents to $contents
     *
     * @param mixed $contents Any fresh contents to save
     * @param array $existing Any existing contents to merge with fresh content
     * @param array $template An optional template to use as the default value for new canisters
     *
     * @return $this
     */
    protected function reset($contents = [], array $existing = [], $template = [])
    {
        //  Clean up inbounds...
        (empty($existing) || !is_array($existing)) && ($existing = []);
        empty($contents) && ($contents = $template ?: []);

        //  i like to move it move it
        $this->contents = new Collection(
            array_merge(
                $this->removeDisallowedKeys($existing),
                $this->removeDisallowedKeys($contents)
            )
        );

        return $this;
    }

    /**
     * Gets a value from the manifest
     *
     * @param string     $key     The manifest key value to retrieve
     * @param mixed|null $default The default value to return if key was not found
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->contents->get($key, $default);
    }

    /**
     * Sets a value in the manifest
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        return $this->put($key, $value);
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param  mixed $key
     *
     * @return $this
     */
    public function forget($key)
    {
        $this->contents->forget($key);

        return $this;
    }

    /**
     * Sets a value in the manifest
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function put($key, $value)
    {
        $this->contents->put($key, $value);

        return $this;
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->contents->all();
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->contents->has($key);
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($key, $value = null)
    {
        return $this->contents->contains($key, $value);
    }

    /**
     * Returns the contents as a \Illuminate\Support\Collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function asCollection()
    {
        return $this->contents;
    }

    /**
     * @return array
     */
    public function getAllowedKeys()
    {
        return $this->allowedKeys;
    }

    /**
     * @param array $allowedKeys
     *
     * @return $this
     */
    public function setAllowedKeys($allowedKeys)
    {
        $this->allowedKeys = $allowedKeys;

        return $this;
    }

    /** @inheritdoc */
    public function jsonSerialize()
    {
        return $this->contents->jsonSerialize();
    }

    /** @inheritdoc */
    public function toArray()
    {
        return $this->contents->toArray();
    }

    /** @inheritdoc */
    public function toJson($options = 0)
    {
        return $this->contents->toJson($options);
    }

    /**
     * Replace case-insensitive tokens in $string with values from me and $data with optional key-wrapping
     *
     * @param string             $string  The target string
     * @param array|\ArrayAccess $data    Extra data to add to replacements
     * @param string             $wrapper The wrapper for keys. Defaults to {}
     *
     * @return string
     */
    public function replace($string, $data = [], $wrapper = null)
    {
        $_values = [];

        if (empty($data) || !is_array($data) || !($data instanceof \ArrayAccess)) {
            $data = [];
        }

        if (!$wrapper || 2 !== strlen($wrapper)) {
            $wrapper = '{}';
        }

        foreach (array_merge($this->toArray(), $data) as $_key => $_value) {
            is_scalar($_value) && $_values[$wrapper[0] . $_key . $wrapper[1]] = $_value;
        }

        return str_ireplace(array_keys($_values), array_values($_values), $string);
    }

    /**
     * Removes any disallowed keys from the $values
     *
     * @param array $values
     *
     * @return array The scrubbed data
     */
    protected function removeDisallowedKeys(array $values = [])
    {
        if (empty($this->allowedKeys)) {
            return $values;
        }

        $_allowed = [];

        foreach ($this->allowedKeys as $_key) {
            if (array_key_exists($_key, $values)) {
                $_allowed[$_key] = $values[$_key];
            }
        }

        return $_allowed;
    }
}