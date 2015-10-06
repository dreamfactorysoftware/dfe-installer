<?php namespace DreamFactory\Enterprise\Common\Listeners;

use DreamFactory\Enterprise\Common\Jobs\BaseJob;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Common\Traits\HasTimer;
use DreamFactory\Enterprise\Common\Traits\Lumberjack;
use DreamFactory\Enterprise\Common\Traits\PublishesResults;
use Psr\Log\LoggerInterface;

/**
 * A base class for listeners. Includes entity lookup and logging
 */
abstract class BaseListener
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The log prefix to use for this handler
     */
    const LOG_PREFIX = null;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use EntityLookup, Lumberjack, HasTimer;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->initializeLumberjack($logger ?: \Log::getMonolog());
        static::LOG_PREFIX && $this->setLumberjackPrefix(static::LOG_PREFIX);
    }

    /**
     * Register's a handler's "handle" method for logging and whatnot
     *
     * @param \DreamFactory\Enterprise\Common\Jobs\BaseJob $job
     *
     * @return $this
     */
    protected function registerHandler(BaseJob $job)
    {
        $this->setLumberjackPrefix(static::LOG_PREFIX ?: str_slug(get_class($this)) . ':' . $job->getJobId());

        return $this;
    }

    /**
     * @param BaseJob     $job      The job/source creating the result
     * @param mixed       $result   The result to store
     * @param string|null $resultId An optional id with which to reference this result. If none given, the $job's ID is used.
     *
     * @return mixed|boolean
     */
    protected function publishResult(BaseJob $job, $result, $resultId = null)
    {
        $_resultId = $resultId ?: $job->getJobId();

        if (!($job instanceof PublishesResults)) {
            return false;
        }

        return $job->publishResult($_resultId, $result);
    }
}
