<?php
namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Utility\AllocationProfile;
use League\Flysystem\Filesystem;

/**
 * Provides file system storage allocation
 */
class StorageAllocationService extends BaseService
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Allocates space on $filesystem based on $profile settings
     *
     * @param AllocationProfile $profile    The allocation profile to follow
     * @param Filesystem        $filesystem The file system on which to allocate the space
     * @param array             $options    Any options necessary to allocate the storage space
     *
     * @return bool True if success
     */
    public function allocate(AllocationProfile $profile, Filesystem $filesystem, $options = [])
    {
        return true;
    }

    /**
     * Deallocates all allocated space on file system
     *
     * @param Filesystem $filesystem The file system from which to deallocate the space
     * @param array      $options
     *
     * @return bool True if all went well
     */
    public function deallocate(Filesystem $filesystem, $options = [])
    {
        return true;
    }

}