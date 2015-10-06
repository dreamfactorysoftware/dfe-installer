<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Database\Models\JobResult;

/**
 * A trait for things that consume published results
 */
trait ConsumesPublishedResults
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $resultId The identifier for which to retrieve the result
     *
     * @return array|null
     */
    public function getPublishedResult($resultId)
    {
        if (null === ($_result = JobResult::byResultId($resultId)->first())) {
            return null;
        }

        return $_result->result_text;
    }

    /**
     * @param string $resultId The identifier to delete
     *
     * @return bool
     */
    public function deletePublishedResult($resultId)
    {
        //  doesn't exist? it's already been deleted as far as we're concerned
        if (null === ($_result = JobResult::byResultId($resultId)->first())) {
            return true;
        }

        return $_result->delete();
    }
}