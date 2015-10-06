<?php namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Contracts\InstanceAware;
use DreamFactory\Enterprise\Database\Models\Instance;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * A base class for all DFE instance-related "job" commands (non-console)
 */
abstract class InstanceJob extends EnterpriseJob implements InstanceAware
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Instance
     */
    protected $instanceId;
    /**
     * @type array
     */
    protected $options;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new command instance.
     *
     * @param string|int $instanceId The instance to provision
     * @param array      $options    Provisioning options
     */
    public function __construct($instanceId, array $options = [])
    {
        $this->instanceId = $instanceId;

        //  Clean up the options and pull them in
        $this->options = [];

        foreach ($options as $_key => $_value) {
            if (!is_numeric($_key)) {
                $this->options[$_key] = $_value;
            }
        }

        parent::__construct(array_get($options, 'cluster-id'),
            array_get($options, 'server-id'),
            array_get($options, 'tag'));
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param bool $includeDefinitions If true, the definitions will be included in the result
     *
     * @return array
     */
    public function getOptions($includeDefinitions = true)
    {
        $_options = $this->options;

        if (!$includeDefinitions) {
            $_options = [];

            //  Strip out any numerically keyed options
            foreach ((array)$this->options as $_key => $_value) {
                !is_numeric($_key) && $_options[$_key] = $_value;
            }
        }

        return $_options;
    }

    /**
     * @return \DreamFactory\Enterprise\Database\Models\Instance|null
     */
    public function getInstance()
    {
        static $_instance;

        if (!$_instance && $this->instanceId) {
            try {
                $_instance = $this->_findInstance($this->instanceId);
            } catch (ModelNotFoundException $_ex) {
                //  ignored
            }
        }

        return $_instance;
    }
}
