<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

if (isset($_REQUEST['ajax_cmd']) && $_REQUEST['ajax_cmd'] == 'anyUpdateAvailable') {
    require_once getModulePath('update_manager_dashboard', true) . '/objects/update_manager_dashboard.php';
    if (UpdateManagerDashboard::anyUpdateAvailable()) {
        echo 'yes';
    } else {
        echo 'no';
    }
    exit();
}
