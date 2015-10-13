<?php namespace DreamFactory\Enterprise\Installer;

use DreamFactory\Enterprise\Common\Providers\InspectionServiceProvider;
use DreamFactory\Library\Utility\Disk;
use DreamFactory\Library\Utility\Exceptions\FileSystemException;
use DreamFactory\Library\Utility\JsonFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class Installer
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The name of the shell script file
     */
    const OUTPUT_FILE_NAME = '.env-install';
    /**
     * @type string The name of the output JSON file
     */
    const JSON_FILE_NAME = '.env-install.json';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $outputFile;
    /**
     * @type string
     */
    protected $jsonFile;
    /**
     * @type array
     */
    protected $formData = [];
    /**
     * @type array
     */
    protected $cleanData = [];
    /**
     * @type array
     */
    protected $facterData = [];
    /**
     * @type array
     */
    protected $defaults = [
        'user'           => 'dfadmin',
        'group'          => 'dfadmin',
        'storage_group'  => 'dfadmin',
        'www_user'       => 'www-data',
        'www_group'      => 'www-data',
        'admin_email'    => null,
        'admin_pwd'      => null,
        'mysql_root_pwd' => null,
        'vendor_id'      => 'dfe',
        'domain'         => null,
        'gh_user'        => null,
        'gh_pwd'         => null,
        'mount_point'    => '/data',
        'storage_path'   => '/storage',
        'log_path'       => '/data/logs',
        'requirements'   => [],
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** ctor  */
    public function __construct()
    {
        $this->formData = $this->cleanData = [];

        $this->outputFile = base_path() . DIRECTORY_SEPARATOR . self::OUTPUT_FILE_NAME;
        $this->jsonFile = base_path() . DIRECTORY_SEPARATOR . self::JSON_FILE_NAME;

        //  If an existing run's data is available, pre-fill form with it
        if (file_exists($this->jsonFile)) {
            logger('Found existing values file "' . $this->jsonFile . '"');

            try {
                $this->defaults = array_merge($this->defaults, JsonFile::decodeFile($this->jsonFile, true));
                logger('Prior values read from "' . $this->jsonFile . '": ' . print_r($this->defaults, true));
            } catch (\Exception $_ex) {
                //  Bogus JSON, just ignore
                logger('No prior values found to seed page. Defaults: ' . print_r($this->defaults, true));
            }
        }

        $this->getRequiredPackages();
    }

    /**
     * Called after form has been submitted. This prepares collected data for writing to
     * source files and sets the member variable $formData
     *
     * @param array $formData
     *
     * @return $this
     */
    public function setFormData(array $formData = [])
    {
        $_facterData = ['#!/bin/sh' . PHP_EOL, 'INSTALLER_FACTS=1'];
        $_cleanData = [];

        if (empty($formData) || count($formData) < 5) {
            /** @noinspection PhpUndefinedMethodInspection */
            Session::flash('failure', 'Not all required fields were completed.');
            /** @noinspection PhpUndefinedMethodInspection */
            Log::error('Invalid number of post entries: ' . print_r($formData, true));

            /** @noinspection PhpUndefinedMethodInspection */
            Redirect::home();
        }

        //  Remove CSRF token
        array_forget($formData, '_token');

        foreach ($formData as $_key => $_value) {
            $_value = trim($_value);

            //  Clean up any diabolical leading slashes on values
            switch ($_key) {
                case 'storage-path':
                    $_storagePath = $_value = trim($_value, DIRECTORY_SEPARATOR);
                    break;

                case 'mount-point':
                    $_mountPoint = $_value = rtrim($_value, DIRECTORY_SEPARATOR);
                    break;
            }

            //  Dump non-empties into the source file
            if (!empty($_value)) {
                $_facterData['FACTER_' . trim(str_replace('-', '_', strtoupper($_key)))] = $_value;
            }

            //  Keep a pristine copy
            $_cleanData[$_key] = $_value;
        }

        //  If set have a storage and mount, construct a storage path
        if (!empty($_storagePath) && !empty($_mountPoint)) {
            $_cleanData['storage-mount-point'] =
            $_facterData['FACTER_STORAGE_MOUNT_POINT'] = Disk::path([$_mountPoint, $_storagePath]);
        }

        $this->formData = $formData;
        $this->facterData = $_facterData;
        $this->cleanData = $_cleanData;

        return $this;
    }

    /**
     * Creates an array of data suitable for writing to a shell script for sourcing
     *
     * @param string $outputFile The output file name. Defaults to "root/.env-install"
     */
    public function writeInstallerFiles($outputFile = self::OUTPUT_FILE_NAME)
    {
        if (empty($this->formData)) {
            throw new \RuntimeException('No form data has been specified. Cannot write blanks.');
        }

        $_sourceFile = $outputFile ?: $this->outputFile;
        $_jsonFile = $outputFile ? $outputFile . '.json' : $this->jsonFile;

        //  Write out the source file
        if (false === file_put_contents($_sourceFile, implode(PHP_EOL, $this->facterData))) {
            throw new FileSystemException('Unable to write output file "' . $_sourceFile . '"');
        }

        //  Write out the JSON file
        try {
            JsonFile::encodeFile($_jsonFile, $this->cleanData);
        } catch (\Exception $_ex) {
            throw new FileSystemException('Unable to write JSON output file "' . $_jsonFile . '"');
        }
    }

    /**
     * Checks the configured requirements for what actually exists on the box
     */
    public function getRequiredPackages()
    {
        $_required = config('dfe.required-packages', []);
        $_service = \App::make(InspectionServiceProvider::IOC_NAME);

        foreach ($_required as $_name => $_packages) {
            if (!is_array($_packages)) {
                $_packages = [$_packages];
            }

            $_hasPackage = false;

            foreach ($_packages as $_package) {
                if (false !== ($_hasPackage = $_service->hasPackage($_package))) {
                    break;
                }
            }

            $this->defaults['requirements'][$_name] = [
                'name'        => $_name,
                'has-package' => $_hasPackage,
                'status'      => $_hasPackage ? 'text-success' : 'text-danger',
            ];
        }
    }

    /**
     * @return array
     */
    public function getFacterData()
    {
        return $this->facterData;
    }

    /**
     * @return string
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * @return string
     */
    public function getJsonFile()
    {
        return $this->jsonFile;
    }

    /**
     * @return array
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @return array
     */
    public function getCleanData()
    {
        return $this->cleanData;
    }
}