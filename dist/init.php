<?php

use App\UliCMS\CoreBootstrap;

// use this constant at the end
// of the page load procedure to measure site performance
define('START_TIME', microtime(true));

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (! defined('CORE_COMPONENT')) {
    throw new Exception('Core Component is not defined');
}

// Define static constants
const CORE_COMPONENT_FRONTEND = 'frontend';
const CORE_COMPONENT_ADMIN = 'admin';
const CORE_COMPONENT_PHPUNIT = 'phpunit';

// Define path constants
define('ULICMS_ROOT', dirname(__FILE__));

// load composer packages
$composerAutoloadFile = ULICMS_ROOT . '/vendor/autoload.php';

if (is_file($composerAutoloadFile)) {
    require_once $composerAutoloadFile;
} else {
    exit('Could not find autoloader. Run \'composer install\'.\n');
}

$coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
$coreBootstrap->init();
