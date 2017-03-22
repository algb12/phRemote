<?php

/**
 * phRemote command handler.
 */
class PhRemoteCMDHandler
{
    /**
     * The host OS (OSX/WIN/LINUX/UNKNOWN)
     * @var string
     */
    public $hostOS;
    /**
     * An array containing all modules and actions
     * @var array
     */
    public $modules;
    public function __construct()
    {
        // Detect host OS
        $this->hostOS = $this->getOS();
        // Load list of modules
        $modules = json_decode(file_get_contents(__DIR__.'/../config/modules.json'), true);
        foreach ($modules['modules'] as $module) {
            $this->modules[$module] = json_decode(file_get_contents(__DIR__."/../modules/$module"), true);
        }
    }

    /**
     * Returns the detected host OS
     * @return string Host OS abbreviation (OSX/WIN/LINUX/UNKNOWN)
     */
    public static function getOS()
    {
        switch (true) {
            case stristr(PHP_OS, 'DAR'): return 'OSX';
            case stristr(PHP_OS, 'WIN'): return 'WIN';
            case stristr(PHP_OS, 'LINUX'): return 'LINUX';
            default : return 'UNKNOWN';
        }
    }

    /**
     * Checks if an action exists in a given module
     * @param  string $module Module file name
     * @param  string $action Action name as defined in module
     * @return bool           Whether the action exists
     */
    public function actionExists($module, $action)
    {
        if (isset($this->modules[$module]['commands'][$action]['cmd'][$this->hostOS])) {
            return true;
        }

        return false;
    }

    /**
     * Executes an action
     * @param  string $module Module file name
     * @param  string $action Action name as defined in module
     * @param  string $params Optional parameters than can be passed to the action
     * @return string         Output of the executed command on host
     */
    public function execAction($module, $action, $params = '')
    {
        error_log('Executing command: '.$this->modules[$module]['commands'][$action]['cmd'][$this->hostOS].PHP_EOL);

        return shell_exec($this->modules[$module]['commands'][$action]['cmd'][$this->hostOS]);
    }

    /**
     * Executes an updater action and returns result in callback
     * @param  string $module Module file name
     * @param  string $action Action name as defined in modules
     * @return array          The result array of the updater(s)
     */
    public function execUpdater($module, $action)
    {
        if (isset($this->modules[$module]['commands'][$action]['updaters'])) {
            $callback = array();
            foreach ($this->modules[$module]['commands'][$action]['updaters'] as $updater) {
                error_log('Executing callback: '.$this->modules[$module]['updaters'][$updater][$this->hostOS].PHP_EOL);
                $callback[$updater]['value'] = shell_exec($this->modules[$module]['updaters'][$updater][$this->hostOS]);
                $callback[$updater]['field'] = $updater;
            }
            error_log('Callback array returned: '.print_r($callback, true).PHP_EOL);

            return $callback;
        }
    }

    /**
     * Returns a JSON array of initial settings
     * @param  string $dataJSON The JSON defining the datafields to be retrieved
     * @return string           The JSON containing the results for the datafields
     */
    public function getInitData($dataJSON)
    {
        error_log('User: '.`whoami`);
        $data = json_decode($dataJSON, true);
        $dataArr = array();

        foreach ($data as $entry) {
            $module = $entry['module'];
            $field = $entry['field'];
            error_log('Getting initial data: '.$this->modules[$module]['init']['datafields'][$field][$this->hostOS].PHP_EOL);
            $value = shell_exec($this->modules[$module]['init']['datafields'][$field][$this->hostOS]);
            $dataArr[$field]['module'] = $module;
            $dataArr[$field]['field'] = $field;
            $dataArr[$field]['value'] = $value;
        }
        $dataJSON = json_encode($dataArr);
        error_log('Request returned: '.$dataJSON.PHP_EOL);

        return $dataJSON;
    }
}
