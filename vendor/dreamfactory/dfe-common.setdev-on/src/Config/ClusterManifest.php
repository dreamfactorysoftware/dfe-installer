<?php namespace DreamFactory\Enterprise\Common\Config;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Common\Enums\EnterprisePaths;
use DreamFactory\Library\Utility\Exceptions\FileException;
use DreamFactory\Library\Utility\IfSet;
use DreamFactory\Library\Utility\JsonFile;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Retrieves, validates, and makes available the DFE cluster manifest, if one exists.
 */
class ClusterManifest implements Arrayable, Jsonable
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $_basePath;
    /**
     * @type string
     */
    protected $_filename;
    /**
     * @type array The contents of the manifest
     */
    protected $_contents;
    /**
     * @type bool True if the manifest was already present
     */
    protected $_existed = false;
    /**
     * @type array The template for the manifest
     */
    private $_template = [
        'cluster-id'                => null,
        'default-domain'            => null,
        'signature-method'          => EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD,
        'storage-root'              => EnterprisePaths::DEFAULT_HOSTED_BASE_PATH,
        'console-api-url'           => null,
        'console-api-key'           => null,
        'console-api-client-id'     => null,
        'console-api-client-secret' => null,
        'dashboard-client-id'       => null,
        'dashboard-client-secret'   => null,
        'client-id'                 => null,
        'client-secret'             => null,
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $basePath The base path of the instance installation
     * @param array  $contents Optional contents to fill
     */
    public function __construct($basePath, $contents = [])
    {
        $_path = rtrim(realpath($basePath), DIRECTORY_SEPARATOR);

        $this->_contents = [];
        $this->_basePath = $_path;
        $this->_filename = EnterpriseDefaults::CLUSTER_MANIFEST_FILE_NAME;

        $this->fill($contents);
    }

    /**
     * @param string $basePath  The base path in which to write the manifest
     * @param array  $contents  The contents of the manifest
     * @param bool   $overwrite If true, any previously generated file will be overwritten
     *
     * @return static
     */
    public static function make($basePath, $contents = [], $overwrite = true)
    {
        /** @type ClusterManifest $_manifest */
        $_manifest = new static($basePath, $contents);
        $_manifest->write($overwrite);

        return $_manifest;
    }

    /**
     * Reads a file and decodes the contents
     *
     * @param string $filename The absolute /path/to/manifest/file
     *
     * @return $this
     */
    public static function createFromFile($filename)
    {
        $_manifest = new static(dirname($filename));

        return $_manifest;
    }

    /**
     * Fill the manifest with fresh contents
     *
     * @param array $contents
     *
     * @return ClusterManifest|$this
     */
    public function fill(array $contents = [])
    {
        foreach ($contents as $_key => $_value) {
            if (array_key_exists($_key, $this->_template)) {
                $this->_contents[$_key] = $_value;
            }
        }

        return $this;
    }

    /**
     * Reads and loads the manifest into memory
     *
     * @return $this
     */
    public function read()
    {
        try {
            $_contents = JsonFile::decodeFile($this->getFullFilename());
        } catch (\InvalidArgumentException $_ex) {
            //  Reset contents to default
            $this->_contents = $this->_template;

            //  No file there...
            return $this;
        }

        if (!empty($_contents)) {
            $_cleaned = $this->_template;

            foreach ($_contents as $_key => $_value) {
                $_key = str_replace(['_', ' '], ['-', null], strtolower(trim($_key)));

                if (array_key_exists($_key, $_cleaned)) {
                    $_cleaned[$_key] = !is_scalar($_value) && !is_array($_value) ? (null === $_value ? null : (array)$_value) : $_value;
                }
            }

            $this->_contents = $_cleaned;
            $this->_existed = true;
        }

        return $this;
    }

    /**
     * Writes the manifest to disk
     *
     * @param bool $overwrite Overwrite any existing manifest
     *
     * @return bool
     */
    public function write($overwrite = true)
    {
        if (empty($this->_contents)) {
            return false;
        }

        $_filename = $this->getFullFilename();

        if (!$overwrite && file_exists($_filename)) {
            throw new FileException('A manifest already exists and $overwrite is set to "FALSE".');
        }

        JsonFile::encodeFile($_filename, $this->_contents);

        return true;
    }

    /**
     * Gets a value from the manifest
     *
     * @param string $key     The manifest key value to retrieve
     * @param mixed  $default The default value to return if key was not found
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_get($this->_contents, $key, $default);
    }

    /**
     * Sets a value in the manifest
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        if (!array_key_exists($key, $this->_contents)) {
            throw new \InvalidArgumentException('The key "' . $key . '" is not valid.');
        }

        $this->_contents[$key] = $value;

        return $this;
    }

    /**
     * @return array The entire manifest
     */
    public function all()
    {
        return $this->_contents;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * @return boolean
     */
    public function existed()
    {
        return $this->_existed;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = JsonFile::DEFAULT_JSON_ENCODE_OPTIONS)
    {
        return JsonFile::encode($this->_contents, $options);
    }

    /**
     * @return string
     */
    public function getFullFilename()
    {
        //  These should be cleaned from construction
        return $this->_basePath . DIRECTORY_SEPARATOR . $this->_filename;
    }
}