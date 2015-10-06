<?php namespace DreamFactory\Enterprise\Common\Bootstrap;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class LoadConfiguration
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        $app->instance('config', $config = new Repository($items));

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        if (!isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        }

        date_default_timezone_set($config['app.timezone']);

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\Repository      $config
     *
     * @return void
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $config)
    {
        $_files = $this->getConfigurationFiles($app);

        foreach ($_files as $key => $path) {
            /** @noinspection PhpIncludeInspection */
            $_data = require $path;

            $config->set($key, $_data);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     *
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $_files = Finder::create()->files()->name('*.php')->in($app->configPath());

        foreach ($_files as $file) {
            $nesting = $this->getConfigurationNesting($file);
            $_key = $nesting . basename($file->getRealPath(), '.php');

            $files[$_key] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return string
     */
    private function getConfigurationNesting(SplFileInfo $file)
    {
        $directory = dirname($file->getRealPath());

        if ($tree = trim(str_replace(config_path(), '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree) . '.';
        }

        return $tree;
    }

}
