<?php

include_once "../App/non_namespaced/UliCMSVersion.php";
$version = new UliCMSVersion();
define("APPLICATION_TITLE", "UliCMS " . $version->getInternalVersionAsString() . " Installation wizard");
