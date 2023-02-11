<?php

session_start();
error_reporting(E_ALL ^ E_NOTICE);

date_default_timezone_set("Europe/Berlin");

define("ULICMS_ROOT", dirname(__FILE__));

foreach (glob('../lib/includes/*.php') as $file) {
    if (is_file($file)) {
        require $file;
    }
}

include_once "controllers/InstallerController.php";

InstallerController::initSessionVars();
if (isset($_GET['language'])) {
    $languages = array(
        'de',
        'en'
    );
    if (in_array($_GET['language'], $languages)) {
        $_SESSION['language'] = $_GET['language'];
    }
}
$lang = InstallerController::getLanguage();

InstallerController::loadLanguageFile($lang);

if (isset($_REQUEST["submit_form"])) {
    if (method_exists("InstallerController", "submit" . $_REQUEST["submit_form"])) {
        call_user_func("InstallerController::submit" . $_REQUEST["submit_form"]);
    }
    die();
}

$step = InstallerController::getStep();

include_once "views/base/top.php";
include_once "views/steps/step" . $step . ".php";
include_once "views/base/bottom.php";
