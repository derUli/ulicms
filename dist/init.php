<?php

use App\Exceptions\SqlException;
use App\Helpers\StringHelper;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Registries\HelperRegistry;
use App\Registries\ModelRegistry;
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
define('ULICMS_CONTENT', ULICMS_ROOT . '/content');
define('ULICMS_TMP', ULICMS_CONTENT . '/tmp');
define('ULICMS_LOG', ULICMS_CONTENT . '/log');
define('ULICMS_GENERATED_PUBLIC', ULICMS_CONTENT . '/generated/public');
define('ULICMS_GENERATED_PRIVATE', ULICMS_CONTENT . '/generated/private');
/**
 * @deprecated since UliCMS 2023.3
 */
define('ULICMS_CONFIGURATIONS', ULICMS_CONTENT . '/configurations');
define('ULICMS_CACHE_BASE', ULICMS_CONTENT . '/cache');
define('ULICMS_CACHE', ULICMS_CACHE_BASE . '/legacy');

// load composer packages
$composerAutoloadFile = ULICMS_ROOT . '/vendor/autoload.php';

if (is_file($composerAutoloadFile)) {
    require_once $composerAutoloadFile;
} else {
    exit('Could not find autoloader. Run \'composer install\'.\n');
}

$coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
$coreBootstrap->setExceptionHandler();

// If there is no new or old config redirect to installer
if(! $coreBootstrap->checkConfigExists() && $coreBootstrap->getInstallerUrl()) {
    Response::redirect($coreBootstrap->getInstallerUrl());
}

$coreBootstrap->initStorages();
$coreBootstrap->loadEnvFile();
$coreBootstrap->createDirectories();
$coreBootstrap->initLoggers();
$coreBootstrap->connectDatabase();

$autoMigrate = isset($_ENV['DBMIGRATOR_AUTO_MIGRATE']) && $_ENV['DBMIGRATOR_AUTO_MIGRATE'];
$additionalSql = isset($_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) ? StringHelper::splitAndTrim($_ENV['DBMIGRATOR_INITIAL_SQL_FILES']) : [];
$additionalSql = array_map('trim', $additionalSql);

if ($autoMigrate) {
    if (is_cli()) {
        Database::setEchoQueries(true);
    }

    $select = Database::setupSchemaAndSelect(
        $_ENV['DB_DATABASE'],
        $additionalSql
    );
} else {
    $select = Database::select($_ENV['DB_DATABASE']);
}

Database::setEchoQueries(false);

if (! $select) {
    throw new SqlException('<h1>Database ' . $_ENV['DB_DATABASE'] . ' doesn\'t exist.</h1>');
}

// Preload all settings for performance reasons
Settings::getAll();

// Run this code only after first call after update
if($coreBootstrap->isFreshDeploy()) {
    $coreBootstrap->postDeployUpdate();
}

$coreBootstrap->registerShutdownFunction();
$coreBootstrap->initLocale();
$coreBootstrap->handleSession();

define('CACHE_PERIOD', (int)Settings::get('cache_period'));

// If setting enforce_https is set redirect http:// to https:///
if ($coreBootstrap->shouldRedirectToSSL()) {
    $coreBootstrap->enforceSSL();
}

ModelRegistry::loadModuleModels();
TypeMapper::loadMapping();
HelperRegistry::loadModuleHelpers();
ControllerRegistry::loadModuleControllers();

do_event('before_init');
do_event('init');
do_event('after_init');

DefaultContentTypes::initTypes();
