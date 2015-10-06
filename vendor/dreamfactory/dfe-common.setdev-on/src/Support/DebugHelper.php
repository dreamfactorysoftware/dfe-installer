<?php namespace DreamFactory\Enterprise\Common\Support;

/**
 * Miscellaneous debugging helpers
 */
class DebugHelper
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Grabs the debug back trace, removes the call to this method and returns a cleaned up array of the trace
     *
     * @param bool|true $clean   If true, trace is cleaned such that "call" will reflect the combination of "file",
     *                           "class", "line", and "type"
     * @param int       $options Options for debug_backtrace()
     * @param int       $limit   The maximum steps to backtrace (defaults to zero, or no limit)
     *
     * @return array
     */
    public static function backtrace($clean = true, $options = DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit = 0)
    {
        $_originalTrace = debug_backtrace($options, $limit);

        //  Remove this call...
        array_shift($_originalTrace);
        $_cleanTrace = [];

        if ($clean) {
            foreach ($_originalTrace as $_step) {
                $_args = data_get($_step, 'args', []);
                $_class = data_get($_step, 'class');
                $_file = data_get($_step, 'file');
                $_line = data_get($_step, 'line');
                $_function = data_get($_step, 'function');
                $_type = data_get($_step, 'type');
                $_object = data_get($_step, 'object');

                //  Clean up some values first...
                ('{closure}' == substr($_function, -9)) && $_function = '{closure}';
                !empty($_line) && $_line = '@' . $_line;

                //  Object method invoked?
                if ($_class && $_type && $_function) {
                    $_entry = ['call' => $_class . $_type . $_function . $_line];
                } else {
                    $_entry = ['call' => $_file . '::' . $_function . $_line];
                }

                $_entry['args'] = json_encode($_args, JSON_UNESCAPED_SLASHES);
                $_entry['object'] = json_encode($_object ?: new \stdClass(), JSON_UNESCAPED_SLASHES);

                $_cleanTrace[] = $_entry;
            }
        }

        return $clean ? $_cleanTrace : $_originalTrace;
    }
}