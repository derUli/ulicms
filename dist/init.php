<?php

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

use App\Exceptions\ConnectionFailedException;
use App\Exceptions\SqlException;
use App\Helpers\StringHelper;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Registries\HelperRegistry;
use App\Registries\ModelRegistry;
use App\Storages\Vars;
use App\UliCMS\CoreBootstrap;

// load composer packages
$composerAutoloadFile = ULICMS_ROOT . '/vendor/autoload.php';

if (is_file($composerAutoloadFile)) {
    require_once $composerAutoloadFile;
} else {
    exit('Could not find autoloader. Run \'composer install\'.\n');
}

if (! is_cli()) {
    set_exception_handler('exception_handler');
}

$coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
$coreBootstrap->initStorages();

// If there is no new or old config redirect to installer
if(! $coreBootstrap->checkConfigExists() && $coreBootstrap->getInstallerUrl()) {
    Response::redirect($coreBootstrap->getInstallerUrl());
}

$coreBootstrap->loadEnvFile();
$coreBootstrap->createDirectories();
$coreBootstrap->initLoggers();

$db_socket = isset($_ENV['DB_SOCKET']) ? (string)$_ENV['DB_SOCKET'] : ini_get('mysqli.default_socket');
$db_port = (int)($_ENV['DB_PORT'] ?? ini_get('mysqli.default_port'));
$db_strict_mode = isset($_ENV['DB_STRICT_MODE']) && $_ENV['DB_STRICT_MODE'];

@$connection = Database::connect(
    $_ENV['DB_SERVER'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    $db_port,
    $db_socket,
    $db_strict_mode
);

if (! $connection) {
    throw new ConnectionFailedException('Can\'t connect to Database.');
}

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

// Preload all settings
Settings::getAll();

// Run this code only after first call after update
if($coreBootstrap->isFreshDeploy()) {
    $coreBootstrap->postDeployUpdate();
}

$coreBootstrap->initLocale();

App\Utils\Session\sessionName(Settings::get('session_name'));
define('CACHE_PERIOD', (int)Settings::get('cache_period'));
date_default_timezone_set(Settings::get('timezone'));

if (isset($_GET['output_stylesheets'])) {
    getCombinedStylesheets();
}

// Session abgelaufen
if (isset($_SESSION['session_begin'])) {
    $session_timeout = 60 * Settings::get('session_timeout');
    if (time() - $_SESSION['session_begin'] > $session_timeout) {
        App\Utils\Session\sessionDestroy();
        send_header('Location: ./');
        exit();
    }
        $_SESSION['session_begin'] = time();
}

register_shutdown_function(
    static function(): void {
        do_event('shutdown');

        $dbmigratorDropDatabaseOnShutdown = isset($_ENV['DBMIGRATOR_DROP_DATABASE_ON_SHUTDOWN']) && $_ENV['DBMIGRATOR_DROP_DATABASE_ON_SHUTDOWN'];

        if ($dbmigratorDropDatabaseOnShutdown) {
            if (is_cli()) {
                Database::setEchoQueries(true);
            }

            Database::dropSchema($_ENV['DB_DATABASE']);
            Database::setEchoQueries(false);
        }
    }
);

define('DEFAULT_MENU', $_ENV['DEFAULT_MENU']);
define('DEFAULT_CONTENT_TYPE', $_ENV['DEFAULT_CONTENT_TYPE']);

$enforce_https = Settings::get('enforce_https');

if (! is_ssl() && $enforce_https) {
    send_header('Location: https://' . $_SERVER['HTTP_HOST'] .
            $_SERVER['REQUEST_URI']);
    exit();
}


$moduleManager = new ModuleManager();
Vars::set('disabledModules', $moduleManager->getDisabledModuleNames());

ModelRegistry::loadModuleModels();
TypeMapper::loadMapping();
HelperRegistry::loadModuleHelpers();
ControllerRegistry::loadModuleControllers();

do_event('before_init');
do_event('init');
do_event('after_init');

DefaultContentTypes::initTypes();
