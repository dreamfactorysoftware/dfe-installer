<?php namespace DreamFactory\Enterprise\Installer;

use DreamFactory\Library\Utility\Disk;
use DreamFactory\Library\Utility\Exceptions\FileSystemException;
use DreamFactory\Library\Utility\JsonFile;
use DreamFactory\Library\Utility\Providers\InspectionServiceProvider;

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
        'user'                  => 'dfadmin',
        'group'                 => 'dfadmin',
        'storage_group'         => 'dfadmin',
        'www_user'              => 'www-data',
        'www_group'             => 'www-data',
        'admin_email'           => null,
        'admin_pwd'             => null,
        'mysql_root_pwd'        => null,
        'vendor_id'             => 'dfe',
        'domain'                => null,
        'gh_user'               => null,
        'gh_pwd'                => null,
        'mount_point'           => '/data',
        'storage_path'          => '/storage',
        'log_path'              => '/data/logs',
        'gh_token'              => null,
        'token_name'            => null,
        'console_host_name'     => 'console',
        'dashboard_host_name'   => 'dashboard',
        'requirements'          => [],
        /** Data Collection */
        'dc_es_exists'          => false,
        'dc_es_cluster'         => 'elasticsearch',
        'dc_es_port'            => 9200,
        'dc_host'               => 'localhost',
        'dc_port'               => 12202,
        'dc_client_host'        => null,
        'dc_client_port'        => 5601,
        /** Installation branches */
        'console_branch'        => 'develop',
        'dashboard_branch'      => 'develop',
        'instance_branch'       => 'develop',
        /** Installation software versions */
        'kibana_version'        => '4.3.0',
        'logstash_version'      => '2.0',
        'elasticsearch_version' => '2.x',
        /** Customisation */
        'custom_css'            => null,
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** ctor  */
    public function __construct()
    {
        $this->formData = $this->cleanData = [];
        $this->defaults['token_name'] = 'dfe-installer-on-' . gethostname() . '-' . date('YmdHis');

        //  Default locations & files
        $this->outputFile = storage_path() . DIRECTORY_SEPARATOR . self::OUTPUT_FILE_NAME;
        $this->jsonFile = storage_path() . DIRECTORY_SEPARATOR . self::JSON_FILE_NAME;

        logger('Output files set to:');
        logger(' > shell source file ' . $this->outputFile);
        logger(' > json source file ' . $this->outputFile);

        //  Branch selections
        $this->defaults = array_merge($this->defaults,
            [
                'console_branch'   => config('dfe.branches.console', 'master'),
                'dashboard_branch' => config('dfe.branches.dashboard', 'master'),
                'instance_branch'  => config('dfe.branches.instance', 'master'),
            ]);

        //  Default versions
        $this->defaults['kibana_version'] = config('dfe.versions.kibana', $this->defaults['kibana_version']);
        $this->defaults['logstash_version'] = config('dfe.versions.logstash', $this->defaults['logstash_version']);
        $this->defaults['elasticsearch_version'] = config('dfe.versions.elasticsearch', $this->defaults['elasticsearch_version']);

        //  If an existing run's data is available, pre-fill form with it
        logger('Checking for last values in "' . $this->jsonFile . '"');

        if (file_exists($this->jsonFile)) {
            logger('Found existing file "' . $this->jsonFile . '"');

            try {
                $_priorData = JsonFile::decodeFile($this->jsonFile, true);

                //  Remove augmented settings
                array_forget($_priorData,
                    [
                        'custom_css_file_source',
                        'custom_css_file_path',
                        'custom_css_file',
                        'login_splash_image_source',
                        'login_splash_image_file',
                        'login_splash_image',
                        'navbar_image_source',
                        'navbar_image_file',
                        'navbar_image',
                    ]);

                $this->defaults = array_merge($this->defaults, $_priorData);
                logger('Values from prior run merged into defaults.');
            } catch (\Exception $_ex) {
                //  Bogus JSON, just ignore
                logger('No prior values found. Using defaults.');
            }
        }

        $this->getRequiredPackages();

        logger('Base operational values set: ' . print_r($this->defaults, true));
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
            \Session::flash('failure', 'Not all required fields were completed.');
            \Log::error('Invalid number of post entries: ' . print_r($formData, true));
            \Redirect::home();
        }

        //  Remove CSRF token
        array_forget($formData, '_token');

        //  Get the domain
        $_domain = trim(array_get($formData, 'domain'));

        //  Incorporate any customisations
        $_customisations = array_merge($formData, $this->getCustomisations($_domain));

        //  Remove transformed form data...
        array_forget($formData, ['custom-css', 'custom_css']);
        array_forget($_customisations, ['custom-css', 'custom_css']);

        logger('Normalised customisation settings: ' . print_r($_customisations, true));
        $formData = array_merge($formData, $_customisations);

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

            $formData[$_key] = $_cleanData[$_cleanKey] = $_facterData['export FACTER_' . strtoupper($_cleanKey)] = $_value ?: '';
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

        //  Add distribution branches
        foreach (config('dfe.branches', []) as $_package => $_branch) {
            $_facterData['export FACTER_' . trim(strtoupper(strtr($_package, '-', '_'))) . '_BRANCH'] = $_branch;
        }

        $this->formData = $formData;
        $this->cleanData = $_cleanData;
        $this->facterData = $_facterData;

        logger('Finalized datasets:');
        logger(' > Original: ' . print_r($this->formData, true));
        logger(' > Normalised: ' . print_r($this->cleanData, true));
        logger(' > FACTER normalised: ' . print_r($this->facterData, true));

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
        if (false === file_put_contents($this->outputFile, '#!/bin/sh' . PHP_EOL . PHP_EOL . implode(PHP_EOL, $_facts) . PHP_EOL . PHP_EOL)) {
            throw new FileSystemException('Unable to write output file "' . $this->outputFile . '"');
        }

        //  Write out the JSON file
        JsonFile::encodeFile($this->jsonFile, $this->cleanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
     *
     * @return array
     */
    protected function getCustomisations($domain)
    {
        $_path = Disk::path([base_path(), static::ASSET_LOCATION], true);
        logger('Custom asset files will be written to: ' . $_path);

        //  Set the customisation defaults
        $_result = [
            'custom-css-file-source'    => null,
            'custom-css-file-path'      => null,
            'custom-css-file'           => null,
            'login-splash-image-source' => null,
            'login-splash-image-path'   => null,
            'login-splash-image'        => null,
            'navbar-image-source'       => null,
            'navbar-image-path'         => null,
            'navbar-image'              => null,
        ];

        //  Check for new custom CSS and write to file...
        $_css = trim(\Input::get('custom-css'));

        if (!empty($_css)) {
            $_fullFile = Disk::path([$_path, $_file = $domain . '-style.css']);

            if (false === file_put_contents($_fullFile, $_css)) {
                throw new \RuntimeException('Unable to write out custom css file "' . $_fullFile . '"');
            }

            $_result['custom-css-file-source'] = $_fullFile;
            $_result['custom-css-file-path'] = $_path;
            $_result['custom-css-file'] = $_file;
        } else {
            logger('No custom CSS found.');

            //  Remove any existing files
            @exec('rm -f ' . Disk::segment([$_path, $domain . '-style.css']));
        }

        return array_merge($_result,
            $this->moveUploadedFile('login-splash-image', $domain, 'logo-dfe'),
            $this->moveUploadedFile('navbar-image', $domain, 'logo-navbar'));
    }

    /**
     * @param string $name     The name of the uploaded field field from the form post
     * @param string $domain   The domain of the upload
     * @param string $fileName The constant part of the result file
     * @param string $location The final destination of the upload
     *
     * @return array
     */
    protected function moveUploadedFile($name, $domain, $fileName, $location = self::ASSET_LOCATION)
    {
        $_result = [];

        //  Make sure our path exists
        $_path = Disk::path([base_path(), $location,], true);

        //  No files? Return empty array
        if (!\Input::hasFile($name) || !\Input::file($name)) {
            //  Remove any existing files from prior runs
            @exec('rm -f ' . Disk::path([$_path, $domain . '-' . $name . '.*']));

            //  Remove any trace of this from the form data
            logger('No "' . $name . '" upload file present in posted data');

            return $_result;
        }

        $_file = \Input::file($name);

        $_sourceFile = $_file->getRealPath();
        $_name = $domain . '-' . trim($fileName, '- ') . '.' . $_file->guessExtension();
        $_fullFile = Disk::path([$_path, $_name]);

        //  Rename/move source file to destination
        if (false === @rename($_sourceFile, $_fullFile)) {
            throw new \RuntimeException('Failed to move uploaded file "' . $_sourceFile . '" to "' . $_fullFile . '".');
        }

        logger('Uploaded file "' . $_sourceFile . '" moved to "' . $_fullFile . '".');

        return [
            $name . '-source' => $_fullFile,
            $name . '-path'   => $_path,
            $name             => $_name,
        ];
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
