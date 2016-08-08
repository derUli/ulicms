<?php
session_start ();
setcookie ( session_name (), session_id () );
error_reporting ( E_ALL ^ E_NOTICE );
include_once "controllers/installer_controller.php";

InstallerController::initSessionVars ();
$lang = InstallerController::getLanguage ();

InstallerController::loadLanguageFile ( $lang );

$step = InstallerController::getStep ();

include_once "views/base/top.php";
include_once "views/steps/step" . $step . ".php";
include_once "views/base/bottom.php";
