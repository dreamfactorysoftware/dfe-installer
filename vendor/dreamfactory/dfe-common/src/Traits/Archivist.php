<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Library\Utility\Disk;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * A trait chocked full of static methods to help with archiving
 */
trait Archivist
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Stream writer with graceful fallback
     *
     * @param Filesystem $filesystem
     * @param string     $source
     * @param string     $destination
     *
     * @return bool
     */
    protected static function writeStream($filesystem, $source, $destination)
    {
        $_resource = false;

        is_resource($source) && $_resource = $source;

        if ($_resource || false !== ($_resource = fopen($source, 'r'))) {
            //  Fallback gracefully if no stream support
            if (method_exists($filesystem, 'writeStream')) {
                $_result = $filesystem->writeStream($destination, $_resource, []);
            } elseif (method_exists($filesystem->getAdapter(), 'writeStream')) {
                $_result = $filesystem->getAdapter()->writeStream($destination, $_resource, $filesystem->getConfig());
            } else {
                $_result = $filesystem->put($destination, file_get_contents($source));
            }

            fclose($_resource);

            return $_result;
        }

        return false;
    }

    /**
     * Force-closes the archive, writing to disk
     *
     * @param \League\Flysystem\Filesystem $filesystem
     *
     * @return bool
     */
    protected static function flush(Filesystem $filesystem)
    {
        static::flushZipArchive($filesystem);
    }

    /**
     * @param Filesystem $source      The source file system to archive
     * @param string     $archiveFile The name of the archive/zip file. Extension is optional, allowing me to decide
     *
     * @return bool|string If successful, the actual file name (without a path) is return. False otherwise
     */
    protected static function archiveTree(Filesystem $source, $archiveFile)
    {
        //  Add file extension if missing
        $archiveFile = static::ensureFileSuffix('.zip', $archiveFile);

        //  Create our zip container
        $_archive = new Filesystem(new ZipArchiveAdapter($archiveFile));

        try {
            foreach ($source->listContents('', true) as $_file) {
                if ('dir' == $_file['type']) {
                    $_archive->createDir($_file['path']);
                } elseif ('link' == $_file['type']) {
                    $_archive->put($_file['path'], $_file['target']);
                } elseif ('file' == $_file['type']) {
                    file_exists($_file['path']) && static::writeStream($_archive, $_file['path'], $_file['path']);
                }
            }
        } catch (\Exception $_ex) {
            \Log::error('Exception exporting instance storage: ' . $_ex->getMessage());

            return false;
        }

        //  Force-close the zip
        static::flushZipArchive($_archive);

        return basename($archiveFile);
    }

    /**
     * Moves a file from the working directory to the destination archive, optionally deleting afterwards.
     *
     * @param Filesystem|\Illuminate\Contracts\Filesystem\Filesystem $archive
     * @param string                                                 $workFile
     * @param bool                                                   $delete If true, file is deleted from work space after being moved
     */
    protected static function moveWorkFile($archive, $workFile, $delete = true)
    {
        if (!is_file($workFile)) {
            throw new \InvalidArgumentException('"' . $workFile . '" is not a file.');
        }

        if (static::writeStream($archive, $workFile, basename($workFile))) {
            $delete && unlink($workFile);
        }
    }

    /**
     * @param string $tag      Unique identifier for temp space
     * @param bool   $pathOnly If true, only the path is returned.
     *
     * @return \League\Flysystem\Filesystem|string
     */
    protected static function getWorkPath($tag, $pathOnly = false)
    {
        if (false === ($_root = Disk::path([sys_get_temp_dir(), 'dfe', $tag], true))) {
            throw new \RuntimeException('Unable to create working directory "' . $_root . '". Aborting.');
        }

        return $pathOnly ? $_root : new Filesystem(new Local($_root));
    }

    /**
     * Deletes a previously made work path
     *
     * @param string $tag Unique identifier for temp space
     *
     * @return bool
     */
    protected static function deleteWorkPath($tag)
    {
        $_root = Disk::path([sys_get_temp_dir(), 'dfe', $tag]);

        return is_dir($_root) ? Disk::rmdir($_root, true) : true;
    }

    /**
     * Ensures a file name has the proper suffix
     *
     * @param string $suffix
     * @param string $file
     *
     * @return string
     */
    protected static function ensureFileSuffix($suffix, $file)
    {
        if ($suffix !== strtolower(substr($file, -(strlen($suffix))))) {
            $file .= $suffix;
        }

        return $file;
    }

    /**
     * Close and reopen a zip archive, forcing writing to disk
     *
     * @param \League\Flysystem\Filesystem $filesystem
     */
    protected static function flushZipArchive(Filesystem $filesystem)
    {
        $_adapter = $filesystem->getAdapter();

        if ($_adapter instanceof ZipArchiveAdapter) {
            $_filename = $_adapter->getArchive()->filename;
            $_adapter->getArchive()->close();
            $_adapter->openArchive($_filename);
        }
    }
}
