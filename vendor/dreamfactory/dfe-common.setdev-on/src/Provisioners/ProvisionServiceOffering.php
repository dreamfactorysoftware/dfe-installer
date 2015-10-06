<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\Offering;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * A provisioner's offering
 */
class ProvisionServiceOffering implements Offering, Jsonable, Arrayable
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string Maximum 64 character short name of offering
     */
    protected $id;
    /**
     * @type string Maximum 64 character short name of offering
     */
    protected $name;
    /**
     * @type string Maximum 1024 character description of offering
     */
    protected $description;
    /**
     * @type array An array of key-value pairs ([:key=>[:option1=>:value1]...]) representing each choice in the offering
     */
    protected $items = [];
    /**
     * @type string The suggested key to use as default when presenting
     */
    protected $suggested;
    /**
     * @type array Any offering config info
     */
    protected $config = [];
    /**
     * @type string The selected choice in $items
     */
    protected $selection;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $id
     * @param array  $values The other values of the offering. These can be one of 'name', 'description', 'items',
     *                       'suggested', or 'config'
     */
    public function __construct($id, $values = [])
    {
        $this->id = $id;

        foreach ($values as $_key => $_value) {
            if ($_key != 'id' && method_exists($this, 'set' . $_key)) {
                $this->{'set' . $_key}($_value);
            }
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return ProvisionServiceOffering
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return ProvisionServiceOffering
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ProvisionServiceOffering
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ProvisionServiceOffering
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     *
     * @return ProvisionServiceOffering
     */
    public function setItems($items)
    {
        if (!is_array($items)) {
            $items = (array)$items;
        }

        $this->items = $items;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuggested()
    {
        return $this->suggested;
    }

    /**
     * @param string $suggested
     *
     * @return ProvisionServiceOffering
     */
    public function setSuggested($suggested)
    {
        $this->suggested = $suggested;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /** @inheritdoc */
    public function toArray()
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'items'       => $this->items,
            'suggested'   => $this->suggested,
            'selection'   => $this->selection,
        ];
    }

    /** @inheritdoc */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
