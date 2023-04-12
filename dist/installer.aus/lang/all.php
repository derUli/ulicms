<?php

include_once '../App/Backend/UliCMSVersion.php';

use App\Backend\UliCMSVersion;

$version = new UliCMSVersion();
define('APPLICATION_TITLE', 'UliCMS ' . $version->getInternalVersionAsString() . ' Installation wizard');
