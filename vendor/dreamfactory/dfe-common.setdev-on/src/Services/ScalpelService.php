<?php
namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Library\Utility\IfSet;
use DreamFactory\Library\Utility\JsonFile;
use Illuminate\Support\Facades\Config;
use Wpb\StringBladeCompiler\Facades\StringView;

/**
 * Provides a service that runs strings pulled from the Config object through the Blade compiler
 */
class ScalpelService extends BaseService
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string $templateKey The config key that holds the Blade template
     * @param array  $data        The data to render
     * @param array  $mergeData   Data to merge with the existing view data
     *
     * @return $this
     */
    public function make($templateKey, $data = [], $mergeData = [])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return
            $this->makeFromString(Config::get($templateKey), $data, $mergeData);
    }

    /**
     * @param string $template  A Blade template read into a string
     * @param array  $data      The data to render
     * @param array  $mergeData Data to merge with the existing view data
     *
     * @return mixed|string
     */
    public function makeFromString($template, $data = [], $mergeData = [])
    {
        $_json = false;
        $_workTemplate = $template;

        !is_string($_workTemplate) && ($_workTemplate = JsonFile::encode($_workTemplate)) && ($_json = true);

        /** @type \Wpb\StringBladeCompiler\StringView $_view */
        /** @noinspection PhpUndefinedMethodInspection */
        $_view = StringView::make(
            [
                'template'   => $_workTemplate,
                'cache_key'  => md5(array_get($data, 'cache_key', microtime(true)) . sha1($_workTemplate)),
                'updated_at' => time(),
            ],
            $data,
            $mergeData
        );

        $_workTemplate = $_view->render();

        return
            $_json
                ? JsonFile::decode($_workTemplate, true)
                : $_workTemplate;
    }

}