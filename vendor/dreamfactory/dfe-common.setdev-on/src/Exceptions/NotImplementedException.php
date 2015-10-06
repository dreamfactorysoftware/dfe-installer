<?php namespace DreamFactory\Enterprise\Common\Exceptions;

use Exception;

/**
 * For when something isn't done
 */
class NotImplementedException extends \Exception
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message ?: 'Feature not implemented.', $code, $previous);
    }
}
