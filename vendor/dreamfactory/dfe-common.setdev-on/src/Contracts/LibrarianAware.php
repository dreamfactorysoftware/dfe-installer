<?php namespace DreamFactory\Enterprise\Services\Contracts;

use DreamFactory\Enterprise\Common\Utility\Librarian;

/**
 * something that manages a librarian
 */
interface LibrarianAware
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The type of compression to use. Can be "zip" or "gz"
     */
    const COMPRESSION_TYPE = 'zip';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return Librarian
     */
    public function getLibrarian();
}