<?php
namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait for services that require run locks
 */
trait LockingService
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The template for the lock file
     */
    protected $_lockFileTemplate = '/var/run/{:tag}.lock';

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string $tag The tag to check
     *
     * @return bool True if a lock file exists
     */
    protected function _isLocked($tag)
    {
        return file_exists($_lockFile = $this->_getLockFile($tag));
    }

    /**
     * @param string $tag The tag to unlock
     *
     * @return bool
     */
    protected function _unlock($tag)
    {
        $_lockFile = $this->_getLockFile($tag);

        $_lockPath = dirname($_lockFile);

        if (!is_dir($_lockPath) && false === mkdir($_lockPath, 0777, true)) {
            throw new \RuntimeException('Unable to create lock file directory. Check permissions.');
        }

        if (false === file_put_contents($_lockFile, getmypid())) {
            throw new \RuntimeException('Unable to create lock file. Check permissions.');
        }

        return true;
    }

    /**
     * @param string $tag The tag to lock
     *
     * @return bool
     */
    protected function _lock($tag)
    {
        if (file_exists($_lockFile = $this->_getLockFile($tag))) {
            if (false === unlink($_lockFile)) {
                throw new \RuntimeException('Unable to remove lock file. Check permissions.');
            }
        }

        return true;
    }

    /**
     * @param string $tag
     *
     * @return string
     */
    protected function _getLockFile($tag)
    {
        return str_replace(['{:tag}', '{:pid}'], [$tag, getmypid()], $this->_lockFileTemplate);
    }
}