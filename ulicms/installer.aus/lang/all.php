<?php
include_once "../version.php";
$version = new ulicms_version ();
define ( "APPLICATION_TITLE", "UliCMS " . $version->getInternalVersionAsString () . " Installation wizard" );