<?php namespace DreamFactory\Enterprise\Common\Jobs;

use DreamFactory\Enterprise\Common\Traits\HasResults;
use DreamFactory\Enterprise\Common\Traits\InteractsWithConsole;
use DreamFactory\Enterprise\Common\Traits\PublishesResults;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Input\InputAwareInterface;

/**
 * A base class for all "job" type commands (non-console)
 */
abstract class BaseJob implements ShouldQueue, InputAwareInterface
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string|bool The queue upon which to push myself. Set to false to not use queuing
     */
    const JOB_QUEUE = false;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string A unique string identifying the job
     */
    protected $jobId;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use InteractsWithConsole, InteractsWithQueue, SerializesModels, HasResults, PublishesResults;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|null $jobId An optional id to assign to the job
     */
    public function __construct($jobId = null)
    {
        $this->jobId = $this->createJobId($jobId);
    }

    /**
     * Publish our results before we die
     */
    function __destruct()
    {
        if (!empty($_result = $this->getResult())) {
            $this->publishResult($this->jobId, $_result);
        }
    }

    /**
     * @return string
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * @param string|null $jobId An optional id to replace the class name portion of the id
     *
     * @return string that identifies this job
     */
    private function createJobId($jobId = null)
    {
        return implode('.',
            [
                '[' . microtime(true) . ']',
                config('dfe.cluster-id'),
                config('dfe.security.console-api-client-id'),
                $jobId
                    ?: str_slug(trim(str_replace(['\\', 'DreamFactory', 'Enterprise',],
                    [' ', null, null,],
                    get_class($this)))),
            ]);
    }
}
