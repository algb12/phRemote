<?php

/**
 * phRemote command handler.
 */
class PhRemoteCMDHandler
{
    public $hostOS;
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

    public static function getOS()
    {
        switch (true) {
            case stristr(PHP_OS, 'DAR'): return 'OSX';
            case stristr(PHP_OS, 'WIN'): return 'WIN';
            case stristr(PHP_OS, 'LINUX'): return 'LINUX';
            default : return 'UNKNOWN';
        }
    }

    public function actionExists($module, $action)
    {
        if (isset($this->modules[$module]['commands'][$action]['cmd'][$this->hostOS])) {
            return true;
        }

        return false;
    }

    public function execAction($module, $action, $params = '')
    {
        error_log('Executing command: '.$this->modules[$module]['commands'][$action]['cmd'][$this->hostOS].PHP_EOL);

        return shell_exec($this->modules[$module]['commands'][$action]['cmd'][$this->hostOS]);
    }

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
        // $callback['value'] = shell_exec($this->modules[$module]['commands'][$action]['callback'][$this->hostOS]);
        // $callback['field'] = $this->modules[$module]['commands'][$action]['datafield'];
    }

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
