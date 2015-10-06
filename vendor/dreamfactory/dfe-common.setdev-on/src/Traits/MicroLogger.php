<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Contracts\Custodial;

/**
 * A trait that keeps a micro "log" of things
 */
trait MicroLogger
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array The collected logs
     */
    private $microLogs = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Adds an entry to the micro log this object
     *
     * @param string     $type   The type of entry (i.e. "provision", "deprovision", "import", etc.)
     * @param array|null $extras Any extra data to log along with the activity
     *
     * @return $this
     */
    public function addMicroLogEntry($type, $extras = [])
    {
        !isset($this->microLogs) && ($this->microLogs = []);

        $this->microLogs[] = [
            $type => array_merge($extras, ['timestamp' => date('c')]),
        ];

        return $this;
    }

    /**
     * Get the collected data
     *
     * @return array
     */
    public function getMicroLogs()
    {
        return $this->microLogs;
    }

    /**
     * @param string $where The key in the manifest to place the micro logs
     * @param bool   $flush if true, any cached entries are cleared
     *
     * @return Custodial
     */
    public function addMicroLogs($where, $flush = false)
    {
        $_logs = $this->getMicroLogs();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->set($where, $_logs);
        $flush && ($this->microLogs = []);

        return $this;
    }
}
