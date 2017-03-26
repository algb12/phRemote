<?php

session_start();

// error_reporting(E_ALL); ini_set('display_errors', 1);

/**
 * phRemote API.
 */

// Require config
require_once __DIR__.'/../config/config.php';

require_once __DIR__.'/PhRemoteCMDHandler.php';
$handler = new PhRemoteCMDHandler();

if (AUTH_ENABLED) {
    if (empty($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] === false) {
        $response['status'] = 'notloggedin';
        $responseJSON = json_encode($response);
        error_log('Request returned: '.$responseJSON.PHP_EOL);
        echo $responseJSON;

        return;
    }
}

$op = $_POST['op'];
@$module = $_POST['module'];
@$action = $_POST['action'];
@$value = $_POST['value'];

if ($op === 'exec' && isset($module) && isset($action)) {
    $response['op'] = $op;
    $response['module'] = $module;
    $response['action'] = $action;
    if ($handler->actionExists($module, $action)) {
        $handler->execAction($module, $action, $value);
        $response['callback'] = $handler->execUpdater($module, $action);
        $response['status'] = 'success';
    } else {
        $response['status'] = 'notfound';
    }
    $responseJSON = json_encode($response);
    error_log('Request returned: '.$responseJSON.PHP_EOL);
    echo $responseJSON;

    return true;
}

$dataJSON = $_POST['dataJSON'];
if ($op === 'getInitData' && isset($dataJSON)) {
    $responseJSON = $handler->getInitData($dataJSON);
    echo $responseJSON;

    return true;
}

$response['status'] = 'badrequest';
$responseJSON = json_encode($response);
error_log('Request returned: '.$responseJSON.PHP_EOL);
echo $responseJSON;

return false;
