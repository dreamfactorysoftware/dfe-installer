<?php namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Provisioners\PortableServiceRequest;

/**
 * A base class for all DFE portability requests
 */
abstract class PortabilityJob extends BaseInstanceJob
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type mixed The job target
     */
    protected $target;
    /**
     * @type mixed Where to send the output
     */
    protected $outputFile;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new portability job
     *
     * @param PortableServiceRequest $request The request
     */
    public function __construct(PortableServiceRequest $request)
    {
        $this->target = $request->getTarget();
        $this->outputFile = $request->get('output-file');
        $this->ownerId = $request->get('owner-id');
        $this->ownerType = $request->get('owner-type');

        parent::__construct($request->getInstanceId(), $request->toArray());
    }

    /**
     * @param mixed $target
     *
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return mixed
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * @param mixed $outputFile
     *
     * @return $this
     */
    public function setOutputFile($outputFile)
    {
        $this->outputFile = $outputFile;

        return $this;
    }
}
