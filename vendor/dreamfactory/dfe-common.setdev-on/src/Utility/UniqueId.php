<?php namespace DreamFactory\Enterprise\Common\Utility;

use DreamFactory\Library\Utility\JsonFile;

class UniqueId
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Generates a unique ID in GUID format
     *
     * @param string $seed Random noise to seed the hash
     *
     * @return string A unique GUID
     */
    public static function generate($seed)
    {
        static $_guid = null;

        $_uuid = uniqid(null, true);
        $_data = $seed . microtime(true) . JsonFile::encode(isset($_SERVER) ? $_SERVER : [microtime(true)]);
        $_hash = strtoupper(hash('ripemd128', $_uuid . $_guid . md5($_data)));

        return
            $_guid =
                substr($_hash, 0, 8) .
                '-' .
                substr($_hash, 8, 4) .
                '-' .
                substr($_hash, 12, 4) .
                '-' .
                substr($_hash, 16, 4) .
                '-' .
                substr($_hash, 20, 12);
    }

}

