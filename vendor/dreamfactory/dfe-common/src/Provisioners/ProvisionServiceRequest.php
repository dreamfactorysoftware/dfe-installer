<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Contracts\PrivatePathAware;
use DreamFactory\Enterprise\Common\Contracts\ResourceProvisioner;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Services\Facades\Provision;
use DreamFactory\Enterprise\Storage\Facades\InstanceStorage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\Flysystem\Filesystem;

class ProvisionServiceRequest extends BaseRequest
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param Filesystem                                        $storage
     * @param bool                                              $deprovision
     * @param bool                                              $force
     * @param array                                             $options Any additional provisioner options
     */
    public function __construct(Instance $instance, Filesystem $storage = null, $deprovision = false, $force = false, array $options = [])
    {
        $_contents = array_merge([
            'instance'    => $instance,
            'instance-id' => $instance->instance_id_text,
            'storage'     => $storage,
            'deprovision' => !!$deprovision,
            'force'       => !!$force,
        ],
            $options);

        parent::__construct($_contents);
    }

    /**
     * Creates a new provisioning request
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param \League\Flysystem\Filesystem|null                 $storage
     * @param bool|false                                        $force
     * @param array                                             $options
     *
     * @return \DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceRequest
     */
    public static function createProvision(Instance $instance, Filesystem $storage = null, $force = false, array $options = [])
    {
        return new ProvisionServiceRequest($instance, $storage, false, $force, $options);
    }

    /**
     * Creates a new deprovisioning request
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     * @param \League\Flysystem\Filesystem|null                 $storage
     * @param bool|false                                        $force
     * @param array                                             $options
     *
     * @return \DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceRequest
     */
    public static function createDeprovision(Instance $instance, Filesystem $storage = null, $force = false, array $options = [])
    {
        return new ProvisionServiceRequest($instance, $storage, true, $force, $options);
    }

    /**
     * @param bool $createIfNull
     *
     * @return Filesystem
     */
    public function getStorage($createIfNull = true)
    {
        //  Use requested file system if one...
        if (null === ($_storage = $this->get('storage')) && $createIfNull) {
            $_instance = $this->getInstance();
            $_user = $_instance->user;

            if (empty($_user)) {
                try {
                    $_user = User::findOrFail($_instance->user_id);
                } catch (ModelNotFoundException $_ex) {
                    \Log::error('Attempt to create an instance for a non-existant user_id: ' . $_instance->user_id);

                    throw new \RuntimeException('Invalid user assigned to instance.');
                }
            }

            InstanceStorage::buildStorageMap($_user->storage_id_text);
            $_storage = $_instance->getStorageRootMount();
            $this->setStorage($_storage);
        }

        return $_storage;
    }

    /**
     * @return boolean
     */
    public function isDeprovision()
    {
        return $this->get('deprovision', false);
    }

    /**
     * @param \League\Flysystem\Filesystem $storage
     *
     * @return ProvisionServiceRequest
     */
    public function setStorage(Filesystem $storage)
    {
        $this->put('storage', $storage);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isForced()
    {
        return $this->get('forced', false);
    }

    /**
     * @param bool|false $force
     *
     * @return $this
     */
    public function setForced($force = false)
    {
        $this->put('force', $force);

        return $this;
    }

    /**
     * @return ResourceProvisioner|PrivatePathAware
     */
    public function getStorageProvisioner()
    {
        if (null === ($_provisioner = $this->get('storage-provisioner'))) {
            $_provisioner = Provision::getStorageProvisioner($this->getInstance()->guest_location_nbr);
            $this->setStorageProvisioner($_provisioner);
        }

        return $_provisioner;
    }

    /**
     * @param ResourceProvisioner $storageProvisioner
     *
     * @return ProvisionServiceRequest
     */
    public function setStorageProvisioner($storageProvisioner)
    {
        $this->put('storage-provisioner', $storageProvisioner);

        return $this;
    }

    /**
     * @return Instance
     */
    public function getInstance()
    {
        return $this->get('instance');
    }

    /**
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     *
     * @return $this
     */
    public function setInstance(Instance $instance)
    {
        $this->put('instance', $instance);

        return $this;
    }

    /**
     * @param int|string $instanceId
     *
     * @return PortableServiceRequest
     */
    protected function setInstanceId($instanceId)
    {
        if (!empty($instanceId)) {
            $this->put('instance', $this->_findInstance($instanceId));
            $this->put('instance-id', $this->getInstance()->instance_id_text);
        }

        return $this;
    }

    /**
     * @return string|int
     */
    public function getInstanceId()
    {
        return $this->get('instance-id');
    }

    /**
     * @return string
     */
    public function getWorkPath()
    {
        return $this->get('work-path');
    }

    /**
     * @param string|mixed $workPath
     *
     * @return $this
     */
    public function setWorkPath($workPath)
    {
        $this->put('work-path', $workPath);

        return $this;
    }
}
