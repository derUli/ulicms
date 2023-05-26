<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\UliCMS\UliCMSVersion;

$version = new UliCMSVersion();
define('APPLICATION_TITLE', 'UliCMS ' . $version->getInternalVersionAsString() . ' Installation wizard');
