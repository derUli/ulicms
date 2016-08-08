<?php
include_once "controllers/installer_controller.php";

$lang = InstallerController::getLanguage();

InstallerController::loadLanguageFile ($lang);

$step = InstallerController::getStep ();

include_once "views/base/top.php";
include_once "views/base/bottom.php";

?>
