<?php
include_once "controllers/installer_controller.php";

InstallerController::loadLanguageFile ();

$step = InstallerController::getStep ();

include_once "views/base/top.php";
include_once "views/base/bottom.php";

?>