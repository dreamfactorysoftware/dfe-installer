<?php
namespace DreamFactory\Enterprise\Common\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Something that can mount/unmount file systems
 */
interface StorageMounter
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Mount the filesystem "$name" as defined in "config/filesystems.php"
     *
     * @param string $name
     * @param array  $options
     *
     * @return Filesystem
     */
    public function mount($name, $options = []);

    /**
     * Unmount the filesystem "$name" as defined in "config/filesystems.php"
     *
     * @param string $name
     * @param array  $options
     *
     * @return StorageMounter
     */
    public function unmount($name, $options = []);
}