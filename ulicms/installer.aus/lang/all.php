<?php

include_once "../classes/UliCMSVersion.php";
$version = new UliCMSVersion();
define("APPLICATION_TITLE", "UliCMS " . $version->getInternalVersionAsString() . " Installation wizard");
