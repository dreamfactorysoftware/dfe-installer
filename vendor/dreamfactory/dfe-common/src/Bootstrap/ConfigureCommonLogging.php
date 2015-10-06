<?php namespace DreamFactory\Enterprise\Common\Bootstrap;

use DreamFactory\Library\Utility\FileSystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging;
use Illuminate\Log\Writer;

class ConfigureEnterpriseLogging extends ConfigureLogging
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $logPath;
    /**
     * @type string
     */
    protected $logFileName;
    /**
     * @type bool
     */
    protected $useCommonLogging = false;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Application $app
     */
    public function bootstrap(Application $app)
    {
        if (isset($app['config'])) {
            error_log('yep');
            $this->logFileName = $app['config']->get('dfe.common.logging.log-file-name');

            if (null !== ($this->logPath = $app['config']->get('dfe.common.logging.log-path'))) {
                $this->logPath = rtrim($this->logPath, ' ' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }

            $this->useCommonLogging =
                (!empty($this->logPath) && !empty($this->logFileName))
                    ? FileSystem::ensurePath($this->logPath)
                    : false;
        } else {
            error_log('nope');
        }

        parent::bootstrap($app);
    }

    /** @inheritdoc */
    protected function configureSingleHandler(Application $app, Writer $log)
    {
        if (!$this->useCommonLogging()) {
            parent::configureSingleHandler($app, $log);

            return;
        }

        $_file = $this->logPath . $this->logFileName;
        $log->useFiles($_file);
    }

    /** @inheritdoc */
    protected function configureDailyHandler(Application $app, Writer $log)
    {
        if (!$this->useCommonLogging()) {
            parent::configureDailyHandler($app, $log);

            return;
        }

        $_file = $this->logPath . $this->logFileName;
        $log->useDailyFiles($_file, $app->make('config')->get('app.log_max_files', 5));
    }

    /**
     * @return boolean
     */
    public function useCommonLogging()
    {
        return $this->useCommonLogging;
    }

    /**
     * @return string
     */
    public function getCommonLogPath()
    {
        return $this->logPath;
    }

    /**
     * @return string
     */
    public function getCommonLogFileName()
    {
        return $this->logFileName;
    }
}