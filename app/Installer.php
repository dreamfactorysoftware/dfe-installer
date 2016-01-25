<?php namespace DreamFactory\Enterprise\Installer;

use DreamFactory\Library\Utility\Disk;
use DreamFactory\Library\Utility\Exceptions\FileSystemException;
use DreamFactory\Library\Utility\Json;
use DreamFactory\Library\Utility\JsonFile;
use DreamFactory\Library\Utility\Providers\InspectionServiceProvider;
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
    /**
     * @type string The relative base path of where custom files are stored.
     */
    const ASSET_LOCATION = 'resources/assets/custom';

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
        'user'                => 'dfadmin',
        'group'               => 'dfadmin',
        'storage_group'       => 'dfadmin',
        'www_user'            => 'www-data',
        'www_group'           => 'www-data',
        'admin_email'         => null,
        'admin_pwd'           => null,
        'mysql_root_pwd'      => null,
        'vendor_id'           => 'dfe',
        'domain'              => null,
        'gh_user'             => null,
        'gh_pwd'              => null,
        'mount_point'         => '/data',
        'storage_path'        => '/storage',
        'log_path'            => '/data/logs',
        'gh_token'            => null,
        'token_name'          => null,
        'console_host_name'   => 'console',
        'dashboard_host_name' => 'dashboard',
        'requirements'        => [],
        /** Data Collection */
        'dc_es_exists'        => false,
        'dc_es_cluster'       => 'elasticsearch',
        'dc_es_port'          => 9200,
        'dc_host'             => 'localhost',
        'dc_port'             => 12202,
        'dc_client_host'      => null,
        'dc_client_port'      => 5601,
        /** Customisation */
        'custom_css'          => null,
        'custom_auth_logo'    => null,
        'custom_nav_logo'     => null,
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** ctor  */
    public function __construct()
    {
        $this->formData = $this->cleanData = [];

        $this->outputFile = storage_path() . DIRECTORY_SEPARATOR . self::OUTPUT_FILE_NAME;
        $this->jsonFile = storage_path() . DIRECTORY_SEPARATOR . self::JSON_FILE_NAME;
        $this->defaults['token_name'] = 'dfe-installer-on-' . gethostname() . '-' . date('YmdHis');

        logger('Output files set to:');
        logger(' > shell source file ' . $this->outputFile);
        logger(' > json source file ' . $this->outputFile);

        logger('Checking for last values in "' . $this->jsonFile . '"');

        //  If an existing run's data is available, pre-fill form with it
        if (file_exists($this->jsonFile)) {
            logger('Found existing file "' . $this->jsonFile . '"');

            try {
                $this->defaults = array_merge($this->defaults, JsonFile::decodeFile($this->jsonFile, true));
                logger('Prior values read from "' . $this->jsonFile . '": ' . print_r($this->defaults, true));
            } catch (\Exception $_ex) {
                //  Bogus JSON, just ignore
                logger('No prior values found to seed page. Defaults: ' . print_r($this->defaults, true));
            }
        }
//        $this->getRequiredPackages();
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
        $_facterData = ['INSTALLER_FACTS' => 1];
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

        //  Incorporate any customisations
        $_domain = trim(array_get($formData, 'domain'));
//        $this->getCustomisations($_domain, $formData);

        //  Add in things that don't exist in form...
        $formData['dc-es-exists'] = array_key_exists('dc-es-exists', $formData) ? 'true' : 'false';
        $formData['dc-es-cluster'] = array_get($formData, 'dc-es-cluster', $this->defaults['dc_es_cluster']);
        $formData['dc-es-port'] = array_get($formData, 'dc-es-port', $this->defaults['dc_es_port']);
        $formData['dc-port'] = array_get($formData, 'dc-port', $this->defaults['dc_port']);
        $formData['dc-client-port'] = array_get($formData, 'dc-client-port', $this->defaults['dc_client_port']);

        //  Check for non-existent host name
        if (empty(array_get($formData, 'dc-host'))) {
            $formData['dc-host'] = implode('.',
                [
                    $this->defaults['console_host_name'],
                    trim(array_get($formData, 'vendor-id')),
                    $_domain,
                ]);
        }

        //  Clean up the keys for factering
        foreach ($formData as $_key => $_value) {
            $_value = trim($_value);
            $_cleanKey = trim(str_replace('-', '_', $_key));

            //  Clean up any diabolical leading slashes on values
            switch ($_cleanKey) {
                case 'storage_path':
                    $_storagePath = $_value = trim($_value, DIRECTORY_SEPARATOR);
                    break;

                case 'mount_point':
                    $_mountPoint = $_value = rtrim($_value, DIRECTORY_SEPARATOR);
                    break;

                case 'dc_host':
                    //  Copy DC host to DC client host
                    $_facterData['export FACTER_DC_CLIENT_HOST'] = $_value;
                    break;
            }

            //  Dump non-empties into the source file
            null !== $_value && $_facterData['export FACTER_' . strtoupper($_cleanKey)] = $_value;

            //  Keep a pristine copy
            $_cleanData[$_cleanKey] = $_value;

            //  Save cleaned value, if any
            $formData[$_key] = $_value;

            unset($_cleanKey, $_key, $_value);
        }

        //  If set have a storage and mount, construct a storage path
        if (!empty($_storagePath) && !empty($_mountPoint)) {
            $_cleanData['storage_mount_point'] = $_facterData['export FACTER_STORAGE_MOUNT_POINT'] = Disk::path([$_mountPoint, $_storagePath]);
        }

        //  Add software versions
        foreach (config('dfe.versions', []) as $_package => $_version) {
            $_facterData['export FACTER_INSTALL_VERSION_' . trim(strtoupper(strtr($_package, '-', '_')))] = $_version;
        }

        $this->formData = $formData;
        $this->cleanData = $_cleanData;
        $this->facterData = $_facterData;

        logger('Form data set: ' . print_r($this->formData, true));
        logger('Clean data set: ' . print_r($this->cleanData, true));
        logger('Facter data set: ' . print_r($this->facterData, true));

        return $this;
    }

    /**
     * Creates an array of data suitable for writing to a shell script for sourcing
     */
    public function writeInstallerFiles()
    {
        if (empty($this->formData)) {
            throw new \RuntimeException('No form data has been specified. Cannot write blanks.');
        }

        //  Fix up fact data
        $_facts = [];
        foreach ($this->facterData as $_key => $_value) {
            $_facts[] = $_key . '=' . $_value;
        }

        //  Write out source file
        if (false === file_put_contents($this->outputFile,
                '#!/bin/sh' . PHP_EOL . PHP_EOL . implode(PHP_EOL, $_facts) . PHP_EOL . PHP_EOL)
        ) {
            throw new FileSystemException('Unable to write output file "' . $this->outputFile . '"');
        }

        //  Write out the JSON file
        if (false === file_put_contents($this->jsonFile,
                Json::encode($this->cleanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
        ) {
            throw new FileSystemException('Unable to write JSON output file "' . $this->jsonFile . '"');
        }
    }

    /**
     * Checks the configured requirements for what actually exists on the box
     */
    public function getRequiredPackages()
    {
        $_requirements = [];
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

            $_requirements[$_name] = [
                'name'        => $_name,
                'has-package' => $_hasPackage,
                'status'      => $_hasPackage ? 'text-success' : 'text-danger',
            ];
        }

        return $this->defaults['requirements'] = $_requirements;
    }

    /**
     * Copies uploaded files to the static asset location locally. Puppet manifests will utilize from there.
     *
     * @param string $domain
     * @param array  $formData
     *
     * @return array
     */
    protected function getCustomisations($domain, array &$formData)
    {
        $_path = Disk::path([base_path(), static::ASSET_LOCATION], true);
        logger('Custom asset path ensured: ' . $_path);

        //  Custom CSS
        if (!empty(($_css = array_get($formData, 'custom-css')))) {
            $_name = $domain . '-style.css';
            if (false === file_put_contents(Disk::path([$_path, $_name]), $_css)) {
                throw new \RuntimeException('Error writing custom css file.');
            }

            logger('Custom CSS written to: ' . $_name);

            $formData['custom-css-file-source'] = Disk::path([$_path, $_name]);
            $formData['custom-css-file-path'] = $_path;
            $formData['custom-css-file'] = $_name;

            array_forget($formData, 'custom-css');
        } else {
            if (file_exists($_file = Disk::path([$_path, $domain . '-style.css']))) {
                @unlink($_file);
            }
        }

        //  Check for auth logo
        if (\Input::file('custom-auth-logo')) {
            try {
                $_name = $domain . '-logo-dfe.' . \Input::file('custom-auth-logo')->guessExtension();

                if (false === @rename(\Input::file('custom-auth-logo')->getRealPath(), Disk::path([$_path, $_name]))) {
                    {
                        throw new \RuntimeException('Authentication logo upload failed to complete successfully.');
                    }
                }

                logger('Custom auth logo written to: ' . $_name);

                $formData['navbar-image-source'] = Disk::path([$_path, $_name]);
                $formData['navbar-image-path'] = $_path;
                $formData['navbar-image'] = $_name;
            } catch (\Exception $_ex) {
            }

            array_forget($formData, 'custom-auth-logo');
        } else {
            if (file_exists($_file = Disk::path([$_path, '-logo-dfe.' . \Input::file('custom-auth-logo')->guessExtension()]))) {
                @unlink($_file);
            }
        }

        //  Check for navbar logo
        if (\Input::file('custom-nav-logo')) {
            try {
                $_name = $domain . '-logo-navbar.' . \Input::file('custom-nav-logo')->guessExtension();

                if (false === @rename(\Input::file('custom-nav-logo')->getRealPath(), Disk::path([$_path, $_name]))) {
                    throw new \RuntimeException('Navigation logo upload failed to complete successfully.');
                }

                logger('Custom nav logo written to: ' . $_name);

                $formData['login-splash-image-source'] = Disk::path([$_path, $_name]);
                $formData['login-splash-image-path'] = $_path;
                $formData['login-splash-image'] = $_name;
            } catch (\Exception $_ex) {
            }
            array_forget($formData, 'custom-nav-logo');
        } else {
            if (file_exists($_file = Disk::path([$_path, $domain . '-logo-navbar.' . \Input::file('custom-nav-logo')->guessExtension()]))) {
                @unlink($_file);
            }
        }

        return $formData;
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
