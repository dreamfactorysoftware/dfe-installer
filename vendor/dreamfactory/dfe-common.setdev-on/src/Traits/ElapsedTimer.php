<?php namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait that adds elapsed timer functionality
 */
trait ElapsedTimer
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array The timers I manage
     */
    protected $elapsedTimers = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Times a closure
     *
     * @param \Closure $closure
     *
     * @return array ['result'=> closure_result, 'elapsed' => elapsed_time
     */
    public function elapsed(\Closure $closure)
    {
        $this->startTimer($_id = spl_object_hash($closure));
        $_result = call_user_func($closure);

        return ['result' => $_result, 'elapsed' => $this->stopTimer($_id)];
    }

    /**
     * @param string $id An optional name for this timer.
     */
    public function startTimer($id = null)
    {
        $elapsedTimers[$this->scrubElapsedTimerId($id)] =
            ['start' => microtime(true), 'end' => null, 'elapsed' => null];
    }

    /**
     * @param null $id
     *
     * @return bool|float Returns the elapsed time or false if no timer exists
     */
    public function stopTimer($id = null)
    {
        if (false === ($_timer = $this->getTimer($id))) {
            return false;
        }

        $this->elapsedTimers[$id]['end'] = microtime(true);

        return $this->elapsedTimers[$id]['elapsed'] =
            $this->elapsedTimers[$id]['end'] - $this->elapsedTimers[$id]['start'];
    }

    /**
     * @param string|null $id
     *
     * @return bool|array Returns the internal timer array structure or false if not found
     */
    public function getTimer($id = null)
    {
        if (array_key_exists($_id = $this->scrubElapsedTimerId($id), $this->elapsedTimers)) {
            return $this->elapsedTimers[$_id];
        }

        return false;
    }

    /**
     * Returns the elapsed time of a timer
     *
     * @param string|null $id
     *
     * @return bool|float The elapsed time of $id, or false if not found or timer still running
     */
    public function getElapsedTime($id = null)
    {
        if (false === ($_timer = $this->getTimer($id)) || null === $_timer['end']) {
            return false;
        }

        return $_timer['elapsed'];
    }

    /**
     * @param string|null $id
     *
     * @return string
     */
    protected function scrubElapsedTimerId($id)
    {
        return $id = $id ?: spl_object_hash($this);
    }

}
