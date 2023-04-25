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

use App\Backend\UliCMSVersion;
use App\Constants\DateTimeConstants;
use App\Exceptions\ConnectionFailedException;
use App\Exceptions\SqlException;
use App\Helpers\StringHelper;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Registries\HelperRegistry;
use App\Registries\LoggerRegistry;
use App\Registries\ModelRegistry;
use App\Storages\Settings\DotEnvLoader;
use App\Storages\Vars;
use App\UliCMS\CoreBootstrap;
use App\Utils\Logger;
use Nette\Utils\FileSystem;

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

$loader = DotEnvLoader::fromEnvironment(ULICMS_ROOT, get_environment());
$loader->load();

// Set default umask for PHP created files
if(isset($_ENV['UMASK'])) {
    umask((string)$_ENV['UMASK']);
}

if ($_ENV['DEBUG']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Create required directories
$createDirectories = [
    ULICMS_TMP,
    ULICMS_CACHE_BASE,
    ULICMS_CACHE,
    ULICMS_LOG,
    ULICMS_GENERATED_PUBLIC,
    ULICMS_GENERATED_PRIVATE,
];

foreach($createDirectories as $dir) {
    if(! is_dir($dir)) {
        FileSystem::createDir($dir);
    }
}

$htaccessForLogFolderSource = ULICMS_ROOT . '/lib/htaccess-deny-all.txt';

// Put .htaccess deny from all to this directories
$secureDirectories =
[
    ULICMS_TMP,
    ULICMS_LOG,
    ULICMS_GENERATED_PRIVATE
];

foreach($secureDirectories as $dir) {
    $htaccessFile = "{$dir}/.htaccess";

    if (! is_file($htaccessFile)) {
        FileSystem::copy($htaccessForLogFolderSource, $htaccessFile);
    }
}

if (isset($_ENV['EXCEPTION_LOGGING']) && $_ENV['EXCEPTION_LOGGING']) {
    LoggerRegistry::register(
        'exception_log',
        new Logger(Path::resolve('ULICMS_LOG/exception_log'))
    );
}

if (isset($_ENV['QUERY_LOGGING']) && $_ENV['QUERY_LOGGING']) {
    LoggerRegistry::register(
        'sql_log',
        new Logger(Path::resolve('ULICMS_LOG/sql_log'))
    );
}

if (isset($_ENV['PHPMAILER_LOGGING']) && $_ENV['PHPMAILER_LOGGING']) {
    LoggerRegistry::register(
        'phpmailer_log',
        new Logger(Path::resolve('ULICMS_LOG/phpmailer_log'))
    );
}

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

$initialized = Settings::get('initialized');

$moduleManager = new ModuleManager();

$version = new UliCMSVersion();
$buildTimestamp = (string)$version->getBuildTimestamp();

// Run this code only after first call after update
if($initialized !== $buildTimestamp) {
    Settings::set('initialized', $buildTimestamp);
    $moduleManager->sync();

    Settings::register('session_name', uniqid() . '_SESSION');
    Settings::register('cache_period', (string)DateTimeConstants::ONE_DAY_IN_SECONDS);
}

App\Utils\Session\sessionName(Settings::get('session_name'));

define('CACHE_PERIOD', (int)Settings::get('cache_period'));

date_default_timezone_set(Settings::get('timezone'));

if (isset($_GET['output_stylesheets'])) {
    getCombinedStylesheets();
}

$locale = Settings::get('locale');

if ($locale) {
    $locale = StringHelper::splitAndTrim($locale);
    array_unshift($locale, LC_ALL);
    @call_user_func_array('setlocale', $locale);
}

$session_timeout = 60 * Settings::get('session_timeout');

// Session abgelaufen
if (isset($_SESSION['session_begin'])) {
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

Vars::set('disabledModules', $moduleManager->getDisabledModuleNames());

ModelRegistry::loadModuleModels();

TypeMapper::loadMapping();
HelperRegistry::loadModuleHelpers();
ControllerRegistry::loadModuleControllers();

do_event('before_init');
do_event('init');
do_event('after_init');

DefaultContentTypes::initTypes();
