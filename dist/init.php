<?php

// use this constant at the end
// of the page load procedure to measure site performance
define('START_TIME', microtime(true));

if (!defined('CORE_COMPONENT')) {
    throw new Exception('Core Component is not defined');
}

// Define static constants
const CORE_COMPONENT_FRONTEND = 'frontend';
const CORE_COMPONENT_ADMIN = 'admin';
const CORE_COMPONENT_PHPUNIT = 'phpunit';

const LF = '\n';  // line feed; Unix
const CRLF = '\r\n'; // carriage return and line feed; Windows
const ONE_DAY_IN_SECONDS = 86400;

// Define path constants
define('ULICMS_ROOT', dirname(__FILE__));
define('ULICMS_TMP', ULICMS_ROOT . '/content/tmp');
define('ULICMS_CACHE_BASE', ULICMS_ROOT . '/content/cache');
define('ULICMS_CACHE', ULICMS_CACHE_BASE . '/legacy');
define('ULICMS_LOG', ULICMS_ROOT . '/content/log');
define('ULICMS_CONTENT', ULICMS_ROOT . '/content');
define('ULICMS_GENERATED', ULICMS_CONTENT . '/generated');
define('ULICMS_CONFIGURATIONS', ULICMS_CONTENT . '/configurations');

use App\Exceptions\ConnectionFailedException;
use App\Exceptions\SqlException;
use App\Registries\HelperRegistry;
use App\Registries\ModelRegistry;
use App\Registries\LoggerRegistry;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Utils\Logger;

// load composer packages
$composerAutoloadFile = dirname(__FILE__) . '/vendor/autoload.php';

if (is_file($composerAutoloadFile)) {
    require $composerAutoloadFile;
} else {
    throw new FileNotFoundException(
        'autoload.php not found. '
        . 'Please run \'./composer install\' to install dependecies.'
    );
}

// Autoloader
spl_autoload_register(function ($className) {
    // Interim solution for not yet namespaced classes
    if (!str_contains($className, '\\')) {
        $className = "App\\non_namespaced\\{$className}";
    }

    $basePath = ULICMS_ROOT . "/{$className}.php";
    $basePath = str_replace('\\', '/', $basePath);

    if (!is_file($basePath)) {
        return;
    }

    require $basePath;
});

// if config exists require_config else redirect to installer
$path_to_config = dirname(__FILE__) . '/CMSConfig.php';

Vars::set('http_headers', []);

// load config file
if (is_file($path_to_config)) {
    require $path_to_config;
} elseif (is_dir('installer')) {
    send_header('Location: installer/');
    exit();
} else {
    throw new Exception('Can\'t require CMSConfig.php. Starting installer failed, too.');
}

if (php_sapi_name() != 'cli') {
    set_exception_handler('exception_handler');
}

global $config;
$config = new CMSConfig();

if (isset($config->debug) && $config->debug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

if (!is_dir(ULICMS_TMP)) {
    mkdir(ULICMS_TMP);
}

if (!is_dir(ULICMS_CACHE_BASE)) {
    mkdir(ULICMS_CACHE_BASE);
}

if (!is_dir(ULICMS_LOG)) {
    mkdir(ULICMS_LOG);
}

if (!is_dir(ULICMS_GENERATED)) {
    mkdir(ULICMS_GENERATED);
}

$htaccessForLogFolderSource = ULICMS_ROOT . '/lib/htaccess-deny-all.txt';
$htaccessLogFolderTarget = ULICMS_LOG . '/.htaccess';
if (!is_file($htaccessLogFolderTarget)) {
    copy($htaccessForLogFolderSource, $htaccessLogFolderTarget);
}


if (isset($config->exception_logging) && $config->exception_logging) {
    LoggerRegistry::register(
        'exception_log',
        new Logger(Path::resolve('ULICMS_LOG/exception_log'))
    );
}
if (isset($config->query_logging) && $config->query_logging) {
    LoggerRegistry::register(
        'sql_log',
        new Logger(Path::resolve('ULICMS_LOG/sql_log'))
    );
}
if (isset($config->phpmailer_logging) && $config->phpmailer_logging) {
    LoggerRegistry::register(
        'phpmailer_log',
        new Logger(Path::resolve('ULICMS_LOG/phpmailer_log'))
    );
}

$db_socket = $config->db_socket ?? ini_get('mysqli.default_socket');
$db_port = $config->db_port ?? ini_get('mysqli.default_port');
$db_strict_mode = $config->db_strict_mode ?? false;

@$connection = Database::connect(
    $config->db_server,
    $config->db_user,
    $config->db_password,
    $db_port,
    $db_socket,
    $db_strict_mode
);

if (!$connection) {
    throw new ConnectionFailedException('Can\'t connect to Database.');
}

$path_to_installer = dirname(__FILE__) . '/installer/installer.php';

if (isset($config->dbmigrator_auto_migrate) && $config->dbmigrator_auto_migrate) {
    $additionalSql = is_array($config->dbmigrator_initial_sql_files) ?
            $config->dbmigrator_initial_sql_files : [];
    if (is_cli()) {
        Database::setEchoQueries(true);
    }
    $select = Database::setupSchemaAndSelect(
        $config->db_database,
        $additionalSql
    );
} else {
    $select = Database::select($config->db_database);
}

Database::setEchoQueries(false);

if (!$select) {
    throw new SqlException('<h1>Database '
                    . $config->db_database . ' doesn\'t exist.</h1>');
}

// Preload all settings
Settings::getAll();

if (!Settings::get('session_name')) {
    Settings::set('session_name', uniqid() . '_SESSION');
}

App\Utils\Session\sessionName(Settings::get('session_name'));

$useragent = Settings::get('useragent');

define(
    'ULICMS_USERAGENT',
    $useragent ?
            $useragent : 'UliCMS Release ' . cms_version()
);

$cache_period = Settings::get('cache_period');

// by Check if the cache expiry is set.
// if not initialize setting with default value
if ($cache_period === null) {
    Settings::set('cache_period', (string) ONE_DAY_IN_SECONDS);
    define('CACHE_PERIOD', ONE_DAY_IN_SECONDS);
} else {
    define('CACHE_PERIOD', $cache_period);
}

date_default_timezone_set(Settings::get('timezone'));

if (isset($_GET['output_stylesheets'])) {
    getCombinedStylesheets();
}

$locale = Settings::get('locale');
if ($locale) {
    $locale = splitAndTrim($locale);
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
    } else {
        $_SESSION['session_begin'] = time();
    }
}

register_shutdown_function(
    function () {
        do_event('shutdown');

        $cfg = new CMSConfig();
        if (isset($cfg->show_render_time) && $cfg->show_render_time && !Request::isAjaxRequest()) {
            echo '\n\n<!--' . (microtime(true) - START_TIME) . '-->';
        }
        if (isset($cfg->dbmigrator_drop_database_on_shutdown) && $cfg->dbmigrator_drop_database_on_shutdown) {
            if (is_cli()) {
                Database::setEchoQueries(true);
            }
            Database::dropSchema($cfg->db_database);
            Database::setEchoQueries(false);
        }
    }
);

$defaultMenu = isset($config->default_menu) && !empty($config->default_menu) ?
        $config->default_menu : 'not_in_menu';
define('DEFAULT_MENU', $defaultMenu);

$defaultContentType = isset($config->default_content_type) && !empty($config->default_menu) ?
        $config->default_content_type : 'page';
define('DEFAULT_CONTENT_TYPE', $defaultContentType);

$enforce_https = Settings::get('enforce_https');

if (!is_ssl() && $enforce_https) {
    send_header('Location: https://' . $_SERVER['HTTP_HOST'] .
            $_SERVER['REQUEST_URI']);
    exit();
}

$moduleManager = new ModuleManager();
$moduleManager->sync();
Vars::set('disabledModules', $moduleManager->getDisabledModuleNames());

ModelRegistry::loadModuleModels();

TypeMapper::loadMapping();
HelperRegistry::loadModuleHelpers();
ControllerRegistry::loadModuleControllers();

do_event('before_init');
do_event('init');
do_event('after_init');

DefaultContentTypes::initTypes();
