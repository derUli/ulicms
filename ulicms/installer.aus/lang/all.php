<?php
include_once "../UliCMSVersion.php";
$version = new UliCMSVersion();
define("APPLICATION_TITLE", "UliCMS " . $version->getInternalVersionAsString() . " Installation wizard");