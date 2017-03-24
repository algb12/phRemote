<?php

session_start();

require_once __DIR__.'/config/config.php';
require __DIR__.'/src/PhRemoteUI.php';

$ui = new PhRemoteUI();

if (AUTH_ENABLED) {
    if (isset($_POST['logout'])) {
        $_SESSION['user_logged_in'] = false;
        unset($_SESSION);
        session_destroy();
    }

    if (isset($_POST['username'])
    && isset($_POST['password'])
    && $_POST['username'] === USERNAME
    && $_POST['password'] === PASSWORD) {
        $_SESSION['user_logged_in'] = true;
    }

    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
        echo $ui->controlUI();
    } else {
        echo $ui->loginUI();
    }
} else {
    echo $ui->controlUI();
}
