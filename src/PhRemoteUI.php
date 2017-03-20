<?php

/**
 * phRemote UI.
 */
class PhRemoteUI
{
    public $modules;
    public function __construct()
    {
        // Load list of modules
        $modules = json_decode(file_get_contents(__DIR__.'/../config/modules.json'), true);
        foreach ($modules['modules'] as $module) {
            $this->modules[$module] = json_decode(file_get_contents(__DIR__."/../modules/$module"), true);
        }
    }

    public function generateUICode($module = '', $action = '')
    {
        $out = <<<EOT
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <link type="text/css" rel="stylesheet" href="web/phRemote.css">
            <script src="web/phRemote.js"></script>
            <title>phRemote prototype</title>
        </head>
        <body>
            <h1>phRemote</h1>

EOT;
        foreach ($this->modules as $module) {
            $out .= '<h2>'.$module['metadata']['label'].'</h2>'.PHP_EOL;
            $out .= '<div class="control_group">'.PHP_EOL;
            foreach ($module['commands'] as $key => $action) {
                $out .= '<button type="button" class="control_btn" onclick="exec(this)" data-module="'.$module['metadata']['module'].'" data-action="'.$key.'">'.$action['label'].'</button>'.PHP_EOL;
            }
            $out .= '</div>'.PHP_EOL;
            $out .= '<div class="module_info">'.PHP_EOL;
            if (isset($module['info'])) {
                foreach ($module['info'] as $key => $field) {
                    $out .= '<div class="field_group">'.PHP_EOL;
                    $out .= '<span class="field_label">'.$field['label'].'</span>'.PHP_EOL;
                    $out .= '<span class="datafield" name="datafield" data-module="'.$module['metadata']['module'].'" data-field="'.$key.'" id="'.$key.'">N/A</span>'.PHP_EOL;
                    $out .= '</div>'.PHP_EOL;
                }
                $out .= '</div>'.PHP_EOL;
            }
        }

        $out .= <<<EOT
        </body>
        </html>
EOT;

        return $out;
    }
}
