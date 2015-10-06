<?php
namespace DreamFactory\Enterprise\Common\Contracts;

use League\Flysystem\FilesystemInterface;

/**
 * Describes a file system aware object
 */
interface FileSystemAware
{
    /**
     * Sets a logger instance on the object
     *
     * @param FilesystemInterface $fileSystem
     *
     * @return $this
     */
    public function setFileSystem(FilesystemInterface $fileSystem);
}