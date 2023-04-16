<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Backend\UliCMSVersion;

$version = new UliCMSVersion();
define('APPLICATION_TITLE', 'UliCMS ' . $version->getInternalVersionAsString() . ' Installation wizard');
