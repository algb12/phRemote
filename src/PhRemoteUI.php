<?php

/**
 * phRemote UI.
 */
class PhRemoteUI
{
    /**
     * An array containing all modules and actions.
     *
     * @var array
     */
    public $modules;
    public function __construct()
    {
        // Load list of modules
        $modules = json_decode(file_get_contents(__DIR__.'/../config/modules.json'), true);
        foreach ($modules['modules'] as $module) {
            $this->modules[$module] = json_decode(file_get_contents(__DIR__."/../modules/$module"), true);
        }

        // Require config
        require_once __DIR__.'/../config/config.php';
    }

    /**
     * Generates the UI for phRemote.
     *
     * @param string $module Module file name
     * @param string $action Action name as defined in modules
     *
     * @return string phRemote HTML markup
     */
    public function controlUI($module = '', $action = '')
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
            $out .= '<div class="module_section">'.PHP_EOL;
            foreach ($module['commands'] as $key => $action) {
                $out .= '<div class="control_group">';
                if (empty($action['input']['type']) || $action['input']['type'] === 'button') {
                    $out .= '<button type="button" class="control_btn" onclick="exec(this)" data-module="'.$module['metadata']['module'].'" data-action="'.$key.'">'.$action['label'].'</button>'.PHP_EOL;
                } elseif ($action['input']['type'] === 'range') {
                    $range_min = isset($action['input']['range_min']) ? $action['input']['range_min'] : 0;
                    $range_max = isset($action['input']['range_max']) ? $action['input']['range_max'] : 100;
                    $out .= '<label for="'.$key.'">'.$action['label'].'</label>'.PHP_EOL;
                    $out .= '<input type="range" class="control_range" name="'.$key.'" min="'.$range_min.'" max="'.$range_max.'" onchange="exec(this)" data-module="'.$module['metadata']['module'].'" data-action="'.$key.'">'.PHP_EOL;
                } elseif ($action['input']['type'] === 'text') {
                    $out .= '<label for="'.$key.'">'.$action['label'].'</label>'.PHP_EOL;
                    $out .= '<input type="text" class="control_text" name="'.$key.'">'.PHP_EOL;
                    $out .= '<button type="button" onclick="exec(this)" data-module="'.$module['metadata']['module'].'" data-action="'.$key.'" data-post-elem="'.$key.'">Update</button>'.PHP_EOL;
                } else {
                    $out .= '<button type="button" class="control_btn" onclick="exec(this)" data-module="'.$module['metadata']['module'].'" data-action="'.$key.'">'.$action['label'].'</button>'.PHP_EOL;
                }
                $out .= '</div>';
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

        if (AUTH_ENABLED === true) {
            $out .= <<<EOT
            <form method="POST" action="">
                <input type="submit" name="logout" value="Log out">
            </form>
EOT;
        }

        $out .= <<<EOT
        </body>
        </html>
EOT;

        return $out;
    }

    public function loginUI()
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
            <h1>phRemote - Log in</h1>
            <form method="POST" action="">
                <div class="form_group username_group">
                    <label for="username">Username:</label>
                    <input type="text" name="username">
                </div>
                <div class="form_group password_group">
                    <label for="password">Password:</label>
                    <input type="password" name="password">
                </div>
                <input type="submit" name="login" value="Log in">
            </form>
        </body>
        </html>
EOT;

        return $out;
    }
}
