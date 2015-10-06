<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Database\Models\JobResult;
use Illuminate\Support\Facades\Log;

/**
 * A trait for things that need to publish a result. Additional functionality for use with
 * \DreamFactory\Enterprise\Common\Traits\HasResults
 */
trait PublishesResults
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Blindly creates a new result key/value row
     *
     * @param string $resultId An identifier to associate with this result. A job or process id for instance
     * @param array  $result   The "result" to publish (i.e. associate value with key $resultId)
     *
     * @return JobResult
     */
    public function publishResult($resultId, $result = [])
    {
        try {
            return JobResult::create(['result_id_text' => $resultId, 'result_text' => $result]);
        } catch (\Exception $_ex) {
            /** @noinspection PhpUndefinedMethodInspection */
            Log::error('exception creating job result row: ' . $_ex->getMessage());

            return false;
        }
    }

    /**
     * Looks up an existing result and updates the value
     *
     * @param string $resultId An identifier to associate with this result. A job or process id for instance
     * @param array  $result   The "result" to publish (i.e. associate value with key $resultId)
     *
     * @return bool
     */
    public function republishResult($resultId, $result = [])
    {
        //  doesn't exist? it's already been deleted as far as we're concerned
        if (null === ($_result = JobResult::byResultId($resultId)->first())) {
            return false !== $this->publishResult($resultId, $result);
        }

        return $_result->update(['result_text' => $result]);
    }

    /**
     * @param string $resultId
     *
     * @return bool|array|mixed
     */
    public function trackResult($resultId)
    {
        /** @var JobResult $_result */
        if (null === ($_result = JobResult::byResultId($resultId)->first())) {
            return false;
        }

        return $_result->result_text;
    }
}
