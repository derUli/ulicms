<?php
include_once "../version.php";
$version = new UliCMSVersion ();
define ( "APPLICATION_TITLE", "UliCMS " . $version->getInternalVersionAsString () . " Installation wizard" );