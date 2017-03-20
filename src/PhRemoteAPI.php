<?php

// error_reporting(E_ALL); ini_set('display_errors', 1);

/**
 * phRemote API.
 */
require 'PhRemoteCMDHandler.php';
$handler = new PhRemoteCMDHandler();

$op = $_POST['op'];
@$module = $_POST['module'];
@$action = $_POST['action'];
@$params = $_POST['params'] ? explode(',', $_POST['params']) : '';

if ($op === 'exec' && isset($module) && isset($action)) {
    $response['op'] = $op;
    $response['module'] = $module;
    $response['action'] = $action;
    if ($handler->actionExists($module, $action)) {
        $handler->execAction($module, $action, $params);
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