<?php

// use this constant at the end
// of the page load procedure to measure site performance
define('START_TIME', microtime(true));

if (! defined('CORE_COMPONENT')) {
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
define('ULICMS_CONTENT', ULICMS_ROOT . '/content');
define('ULICMS_TMP', ULICMS_CONTENT . '/tmp');
define('ULICMS_LOG', ULICMS_CONTENT . '/log');
define('ULICMS_GENERATED_PUBLIC', ULICMS_CONTENT . '/generated/public');
define('ULICMS_GENERATED_PRIVATE', ULICMS_CONTENT . '/generated/private');
define('ULICMS_CONFIGURATIONS', ULICMS_CONTENT . '/configurations');
define('ULICMS_CACHE_BASE', ULICMS_CONTENT . '/cache');
define('ULICMS_CACHE', ULICMS_CACHE_BASE . '/legacy');

use App\Backend\UliCMSVersion;
use App\Exceptions\ConnectionFailedException;
use App\Exceptions\SqlException;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Registries\HelperRegistry;
use App\Registries\LoggerRegistry;
use App\Registries\ModelRegistry;
use App\Utils\Logger;
use Nette\Utils\FileSystem;

// TODO: refactor Bootstrap to a new UliCMSBoostrap Class which is splitted into methods

// load composer packages
$composerAutoloadFile = ULICMS_ROOT . '/vendor/autoload.php';

if (is_file($composerAutoloadFile)) {
    require $composerAutoloadFile;
} else {
    exit('Could not find autoloader. Run \'composer install\'.\n');
}

// if config exists require_config else redirect to installer
$path_to_config = dirname(__FILE__) . '/CMSConfig.php';

\App\Storages\Vars::set('http_headers', []);

// load config file
if (is_file($path_to_config)) {
    require $path_to_config;
} elseif (is_dir('installer')) {
    send_header('Location: installer/');
    exit();
} else {
    throw new Exception('Can\'t require CMSConfig.php. Starting installer failed, too.');
}

if (PHP_SAPI !== 'cli') {
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

// Create required directories
$createDirectories = [
    ULICMS_TMP,
    ULICMS_CACHE_BASE,
    ULICMS_CACHE,
    ULICMS_LOG,
    ULICMS_GENERATED_PUBLIC,
    ULICMS_GENERATED_PRIVATE,
];

foreach($createDirectories as $dir){
    if(! is_dir($dir)){
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

foreach($secureDirectories as $dir){
    $htaccessFile = "{$dir}/.htaccess";

    if (! is_file($htaccessFile)) {
        FileSystem::copy($htaccessForLogFolderSource, $htaccessFile);
    }
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

if (! $connection) {
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

if (! $select) {
    throw new SqlException('<h1>Database '
                    . $config->db_database . ' doesn\'t exist.</h1>');
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
    Settings::register('cache_period', (string)ONE_DAY_IN_SECONDS);
}

App\Utils\Session\sessionName(Settings::get('session_name'));

define('CACHE_PERIOD', (int)Settings::get('cache_period'));

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
    }
        $_SESSION['session_begin'] = time();

}

register_shutdown_function(
    static function() {
        do_event('shutdown');

        $cfg = new CMSConfig();
        if (isset($cfg->show_render_time) && $cfg->show_render_time && ! Request::isAjaxRequest()) {
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

$defaultMenu = isset($config->default_menu) && ! empty($config->default_menu) ?
        $config->default_menu : 'not_in_menu';
define('DEFAULT_MENU', $defaultMenu);

$defaultContentType = isset($config->default_content_type) && ! empty($config->default_menu) ?
        $config->default_content_type : 'page';
define('DEFAULT_CONTENT_TYPE', $defaultContentType);

$enforce_https = Settings::get('enforce_https');

if (! is_ssl() && $enforce_https) {
    send_header('Location: https://' . $_SERVER['HTTP_HOST'] .
            $_SERVER['REQUEST_URI']);
    exit();
}

\App\Storages\Vars::set('disabledModules', $moduleManager->getDisabledModuleNames());

ModelRegistry::loadModuleModels();

TypeMapper::loadMapping();
HelperRegistry::loadModuleHelpers();
ControllerRegistry::loadModuleControllers();

do_event('before_init');
do_event('init');
do_event('after_init');

DefaultContentTypes::initTypes();
