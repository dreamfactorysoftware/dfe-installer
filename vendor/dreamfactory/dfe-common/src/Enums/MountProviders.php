<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The providers currently supported
 */
class MountProviders extends FactoryEnum
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @type int Amazon S3 */
    const AMAZON_S3 = 0;
    /** @type int ACL-aware S3 */
    const AMAZON_S3_ACL = 1;
    /** @type int Azure Blob */
    const AZURE_BLOB = 2;
    /** @type int Doctrine's DBAL */
    const DOCTRINE_DBAL = 3;
    /** @type int DropBox */
    const DROPBOX = 5;
    /** @type int FTP */
    const FTP = 6;
    /** @type int GridFS on MongoDB */
    const GRID_FS = 7;
    /** @type int Local file system */
    const LOCAL = 8;
    /** @type int Local file system stream-wrapped */
    const LOCAL_STREAM = 9;
    /** @type int Safe local file system (uses hashes instead of directories) */
    const LOCAL_SAFE = 10;
    /** @type int In-memory file system */
    const MEMORY = 11;
    /** @type int OpenCloud */
    const OPEN_CLOUD = 12;
    /** @type int Laze OpenClond */
    const OPEN_CLOUD_LAZY = 13;
    /** @type int SFTP (via ssh) */
    const SFTP = 14;
    /** @type int Standard ZIP archive */
    const ZIP_ARCHIVE = 15;
}
