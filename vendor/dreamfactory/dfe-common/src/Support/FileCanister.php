<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Contracts\Custodial;
use DreamFactory\Enterprise\Common\Traits\Custodian;
use DreamFactory\Library\Utility\Exceptions\FileException;
use DreamFactory\Library\Utility\Json;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * Reads/writes a canister to a file
 */
class FileCanister extends Canister implements Custodial
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Custodian;

    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type int The number of times to retry write operations (default is 3)
     */
    const STORAGE_OPERATION_RETRY_COUNT = 3;
    /**
     * @type int The number of times to retry write operations (default is 5s)
     */
    const STORAGE_OPERATION_RETRY_DELAY = 500000;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type bool If true, a copy of files to be overwritten will be made
     */
    protected $makeBackups = true;
    /**
     * @type Filesystem The filesystem where the file lives
     */
    protected $filesystem;
    /**
     * @type string The name of the file
     */
    protected $filename;
    /**
     * @type array The default template, or structure, to use when creating a new object
     */
    protected $template = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param mixed                        $contents    The contents to write to the file if being created
     * @param string                       $filename    The name of the file, relative to base path or absolute
     * @param \League\Flysystem\Filesystem $filesystem  The optional filesystem on which this file lives
     * @param array                        $template    An optional array to use as a default value
     * @param bool                         $makeBackups If true, a copy of files to be overwritten will be made
     */
    public function __construct($contents = [], $filename, Filesystem $filesystem = null, array $template = [], $makeBackups = true)
    {
        $this->makeBackups = $makeBackups;

        //  Map our file system root to parent of $filename
        if (!$filesystem) {
            $_path = dirname(realpath($filename));

            if (empty($_path)) {
                $_path = base_path();
            }

            $filesystem = new Filesystem(new Local($_path));
        }

        $this->filesystem = $filesystem;
        $this->filename = $filename;
        $this->template = $template;

        parent::__construct();

        $this->loadExisting($contents);
    }

    /**
     * Creates a new canister and writes it immediately to file
     *
     * @param mixed           $contents
     * @param string          $filename
     * @param Filesystem|null $filesystem
     * @param array           $template
     * @param bool|true       $makeBackups
     *
     * @return static
     */
    public static function create($contents = [], $filename, Filesystem $filesystem = null, array $template = [], $makeBackups = true)
    {
        $_canister = new static($contents, $filename, $filesystem, $template, $makeBackups);
        $_canister->write();

        return $_canister;
    }

    /**
     * @param string                            $filename The file name, abolute or relative to $filesystem
     * @param \League\Flysystem\Filesystem|null $filesystem
     *
     * @return $this
     */
    public static function createFromFile($filename, Filesystem $filesystem = null)
    {
        //  What are we dealing with?
        if (null === $filesystem) {
            if (!file_exists($filename) || !is_readable($filename)) {
                throw new \InvalidArgumentException('The filename "' . $filename . '" is invalid or not found.');
            }
        } else if (!$filesystem->has($filename)) {
            throw new \InvalidArgumentException('The filename "' . $filename . '" was not found on $filesystem.');
        }

        return static::create([], $filename, $filesystem);
    }

    /**
     * Loads the existing canister and resets the collection with the contents and existing data merged
     *
     * @param array $contents The contents with which to initialize. Merged with existing data
     */
    protected function loadExisting($contents = [])
    {
        //  See if we have an existing file...
        try {
            $this->reset($contents, $this->read(false) ?: []);
        } catch (\Exception $_ex) {
            //  Ignored but noted
        }
    }

    /**
     * Reads and loads the contents
     *
     * @param bool $reset     If true (the default) the contents are reset and loaded from the file. If false, the
     *                        data is returned but the current contents are left undisturbed.
     *
     * @return array
     */
    public function read($reset = true)
    {
        $_contents = $this->doRead(true);

        if ($reset && !empty($_contents)) {
            $this->reset($_contents)->addActivity('read')->addCustodyLogs(static::CUSTODY_LOG_KEY);
        }

        return $_contents;
    }

    /**
     * Reads the file and returns the contents
     *
     * @param bool $decoded If true (the default), the read data is decoded
     * @param int  $depth   The maximum recursion depth
     * @param int  $options Any json_decode options
     *
     * @return array|bool|string
     */
    protected function doRead($decoded = true, $depth = 512, $options = 0)
    {
        //  Always start with an empty array
        $_result = $this->template;

        //  No existing file, empty array back
        if (!$this->filename || !$this->filesystem->has($this->filename)) {
            return $_result;
        }

        //  Error reading file, false back...
        if (false === ($_json = $this->filesystem->read($this->filename))) {
            return false;
        }

        //  Not decoded gets string back
        if (!$decoded) {
            return $_json;
        }

        return Json::decode($_json, true, $depth, $options);
    }

    /**
     * Writes the contents to disk
     *
     * @param bool $overwrite Overwrite an existing file
     *
     * @return bool
     */
    public function write($overwrite = true)
    {
        if (empty($this->contents)) {
            $this->reset($this->template);
        }

        if (!$overwrite && $this->filesystem->has($this->filename)) {
            throw new FileException('The file "' . $this->filename . '" already exists, and $overwrite is set to "FALSE".');
        }

        $this->addActivity('write')->addCustodyLogs(static::CUSTODY_LOG_KEY, true);

        return $this->doWrite();
    }

    /**
     * Writes the file
     *
     * @param int       $options    Any JSON encoding options
     * @param int       $depth      The maximum recursion depth
     * @param int       $retries    The number of times to retry the write.
     * @param float|int $retryDelay The number of microseconds (100000 = 1s) to wait between retries
     *
     * @return bool
     */
    protected function doWrite($options = 0, $depth = 512, $retries = self::STORAGE_OPERATION_RETRY_COUNT, $retryDelay = self::STORAGE_OPERATION_RETRY_DELAY)
    {
        $_attempts = $retries;

        //  Let's not get cray-cray
        if ($_attempts < 1) {
            $_attempts = 1;
        }

        if ($_attempts > 5) {
            $_attempts = 5;
        }

        $this->backupExistingFile();

        $_contents = $this->toArray();

        if (empty($_contents) || !is_array($_contents)) {
            //  Always use the current template
            $_contents = $this->template;
        }

        while ($_attempts--) {
            try {
                if ($this->filesystem->put($this->filename, Json::encode($_contents, $options, $depth))) {
                    break;
                }
                throw new FileException('Unable to write data to file "' . $this->filename . '" after ' . $retries . ' attempt(s).');
            } catch (FileException $_ex) {
                if ($_attempts) {
                    usleep($retryDelay);
                    continue;
                }

                throw $_ex;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function backupExistingFile()
    {
        //  No backups needed...
        if (!$this->makeBackups || !$this->filesystem->has($this->filename)) {
            return true;
        }

        //  Copy the file...
        if (!$this->filesystem->copy($this->filename, $this->filename . date('YmdHiS') . '.save')) {
            \Log::error('Unable to make backup copy of "' . $this->filename . '"');

            return false;
        }

        return true;
    }
}