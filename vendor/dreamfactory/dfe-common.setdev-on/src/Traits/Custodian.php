<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Contracts\Custodial;

/**
 * A trait that keeps track of things checked in and out
 * @implements \DreamFactory\Enterprise\Common\Contracts\Custodial
 */
trait Custodian
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array Data to write to manifest
     */
    private $custodialActivity = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Adds an entry to the specified custodial "log" for this object
     *
     * @param string     $activity A "name" denoting the activity performed. ie. "import" or "export"
     * @param array|null $extras   Any extra data to log along with the activity
     *
     * @return Custodial
     */
    public function addActivity($activity, array $extras = [])
    {
        !isset($this->custodialActivity) && ($this->custodialActivity = []);

        $this->custodialActivity[] = [
            $activity => array_merge($extras, ['timestamp' => date('c')]),
        ];

        return $this;
    }

    /**
     * Get the logged activity
     *
     * @return array
     */
    public function getActivities()
    {
        return $this->custodialActivity;
    }

    /**
     * @param string $where The key in the manifest to place the custody logs. Defaults to "_custodian".
     * @param bool   $flush if true, any cached entries are cleared
     *
     * @return Custodial
     */
    public function addCustodyLogs($where = Custodial::CUSTODY_LOG_KEY, $flush = false)
    {
        $_activities = $this->getActivities();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->set($where, $_activities);
        $flush && ($this->custodialActivity = []);

        return $this;
    }

}
