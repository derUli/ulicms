<?php
session_start ();
setcookie ( session_name (), session_id () );
error_reporting ( E_ALL ^ E_NOTICE );
include_once "controllers/installer_controller.php";

InstallerController::initSessionVars ();
if (isset ( $_GET ["language"] )) {
	$languages = array (
			"en" 
	);
	if (in_array ( $_GET ["language"], $languages )) {
		$_SESSION ["language"] = $_GET ["language"];
	}
}
$lang = InstallerController::getLanguage ();

InstallerController::loadLanguageFile ( $lang );

if (isset ( $_REQUEST ["submit_form"] )) {
	
	if (method_exists ( InstallerController, "submit" . $_REQUEST ["submit_form"] )) {
		call_user_func ( "InstallerController::submit" . $_REQUEST ["submit_form"] );
	}
	die ();
}

$step = InstallerController::getStep ();

include_once "views/base/top.php";
include_once "views/steps/step" . $step . ".php";
include_once "views/base/bottom.php";
