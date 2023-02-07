<?php

if (!defined('CORE_COMPONENT')) {
    throw new Exception('Core Component is not defined');
}

// Define static constants
const CORE_COMPONENT_FRONTEND = 'frontend';
const CORE_COMPONENT_ADMIN = 'admin';
const CORE_COMPONENT_PHPUNIT = 'phpunit';

const CR = "\r"; // carriage return; Mac
const LF = "\n";  // line feed; Unix
const CRLF = "\r\n"; // carriage return and line feed; Windows
const ONE_DAY_IN_SECONDS = 86400;

// use this constant at the end
// of the page load procedure to measure site performance
define("START_TIME", microtime(true));

// root directory of UliCMS
if (!defined("ULICMS_ROOT")) {
    define("ULICMS_ROOT", dirname(__FILE__));
}

use App\Exceptions\AccessDeniedException;
use App\Exceptions\ConnectionFailedException;
use App\Exceptions\SqlException;
use App\Registries\HelperRegistry;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;

// load composer packages
$composerAutoloadFile = dirname(__FILE__) . "/vendor/autoload.php";

if (file_exists($composerAutoloadFile)) {
    require $composerAutoloadFile;
} else {
    throw new FileNotFoundException(
                    "autoload.php not found. "
                    . "Please run \"./composer install\" to install dependecies."
    );
}

// Autoloader
spl_autoload_register(function ($className) {
    // Backwards compatiblity for old code

    if (str_starts_with($className, 'UliCMS\\')) {
        trigger_error(
                "Namespaces starting with UliCMS\\ are deprecated: $className",
                E_USER_DEPRECATED
        );

        $className = 'App\\' . substring($className, 8);
    }

    // Interim solution for non namespaced classes
    if (!str_contains($className, "\\")) {
        $className = "App\\non_namespaced\\{$className}";
    }

    $basePath = dirname(__FILE__) . "/{$className}.php";
    $basePath = str_replace('\\', '/', $basePath);

    if (!file_exists($basePath)) {
        return;
    }

    require $basePath;
});

function require_all_files_in_dir(string $dir) {
    $files = glob(ULICMS_ROOT . "/$dir/*.php");

    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'load.php') {
            require $file;
        }
    }
}

require dirname(__FILE__) . "/lib/load.php";

$loadDirs = [
    'classes/objects/constants',
];

foreach ($loadDirs as $loadDir) {
    require_all_files_in_dir($loadDir);
}

require dirname(__FILE__) . "/classes/objects/storages/load.php";
require dirname(__FILE__) . "/classes/objects/modules/load.php";
require dirname(__FILE__) . "/classes/objects/settings/load.php";
require dirname(__FILE__) . "/classes/objects/web/load.php";
require dirname(__FILE__) . "/classes/objects/content/types/fields/load.php";
require dirname(__FILE__) . "/classes/objects/registry/load.php";
require dirname(__FILE__) . "/classes/objects/html/load.php";
require dirname(__FILE__) . "/classes/objects/database/load.php";
require dirname(__FILE__) . "/classes/objects/security/load.php";
require dirname(__FILE__) . "/classes/objects/files/load.php";
require dirname(__FILE__) . "/classes/objects/users/load.php";
require dirname(__FILE__) . "/classes/objects/content/CustomData.php";
require dirname(__FILE__) . "/classes/objects/content/Content.php";
require dirname(__FILE__) . "/classes/objects/content/Page.php";
require dirname(__FILE__) . "/classes/objects/content/Snippet.php";
require dirname(__FILE__) . "/classes/objects/content/Link.php";
require dirname(__FILE__) . "/classes/objects/content/Language_Link.php";
require dirname(__FILE__) . "/classes/objects/content/Node.php";
require dirname(__FILE__) . "/classes/objects/content/List_Data.php";
require dirname(__FILE__) . "/classes/objects/content/Content_List.php";
require dirname(__FILE__) . "/classes/objects/content/Module_Page.php";
require dirname(__FILE__) . "/classes/objects/content/Video_Page.php";
require dirname(__FILE__) . "/classes/objects/content/Audio_Page.php";
require dirname(__FILE__) . "/classes/objects/content/Image_Page.php";
require dirname(__FILE__) . "/classes/objects/content/Article.php";
require dirname(__FILE__) . "/classes/objects/content/ContentFactory.php";
require dirname(__FILE__) . "/classes/objects/content/CustomFields.php";
require dirname(__FILE__) . "/classes/objects/content/Results.php";

$mobile_detect_as_module = dirname(__FILE__) .
        "/content/modules/Mobile_Detect/Mobile_Detect.php";

if (file_exists($mobile_detect_as_module)) {
    require $mobile_detect_as_module;
}

function exception_handler($exception) {
    if (!defined("EXCEPTION_OCCURRED")) {
        define("EXCEPTION_OCCURRED", true);
    }

    // FIXME: what if there is no config class?
    $cfg = class_exists("CMSConfig") ? new CMSConfig() : null;

    $message = $cfg && $cfg->debug ?
            $exception : "An error occurred! See exception_log for details. 😞";

    $logger = LoggerRegistry::get("exception_log");
    if ($logger) {
        $logger->error($exception);
    }
    $httpStatus = $exception instanceof AccessDeniedException ?
            HttpStatusCode::FORBIDDEN : HttpStatusCode::INTERNAL_SERVER_ERROR;
    if (function_exists("HTMLResult") && class_exists("Template") && !headers_sent() && function_exists("get_theme")) {
        ViewBag::set("exception", nl2br(_esc($exception)));
        HTMLResult(Template::executeDefaultOrOwnTemplate("exception.php"), $httpStatus);
    }

    echo "{$message}\n";
}

// if config exists require_config else redirect to installer
$path_to_config = dirname(__FILE__) . "/CMSConfig.php";

Vars::set("http_headers", []);

// load config file
if (file_exists($path_to_config)) {
    require $path_to_config;
} elseif (is_dir("installer")) {
    send_header("Location: installer/");
    exit();
} else {
    throw new Exception("Can't require CMSConfig.php. Starting installer failed, too.");
}

if (php_sapi_name() != "cli") {
    set_exception_handler('exception_handler');
}

global $config;
$config = new CMSConfig();

// IF ULICMS_DEBUG is defined then display all errors except E_NOTICE,
// else disable error_reporting from php.ini
if ((defined("ULICMS_DEBUG") && ULICMS_DEBUG) || (isset($config->debug) && $config->debug)) {
    error_reporting(E_ALL ^ E_NOTICE);
} else {
    error_reporting(0);
}

/**
 * @deprecated since version 2023.1
 */
define("ULICMS_DATA_STORAGE_ROOT", ULICMS_ROOT);

if (!defined("ULICMS_TMP")) {
    define("ULICMS_TMP", ULICMS_ROOT . "/content/tmp/");
}

if (!is_dir(ULICMS_TMP)) {
    mkdir(ULICMS_TMP);
}

if (!defined("ULICMS_CACHE_BASE")) {
    define("ULICMS_CACHE_BASE", ULICMS_ROOT . "/content/cache");
}

// Todo: Alle stellen, wo manuell Cache-Dateien geschrieben werden auf PhpFastCache umstellen
// und das hier dann abschaffen.
if (!defined("ULICMS_CACHE")) {
    define("ULICMS_CACHE", ULICMS_CACHE_BASE . "/legacy");
}

if (!defined("ULICMS_LOG")) {
    define("ULICMS_LOG", ULICMS_ROOT . "/content/log/");
}
if (!defined("ULICMS_CONTENT")) {
    define("ULICMS_CONTENT", ULICMS_ROOT . "/content/");
}

if (!defined("ULICMS_GENERATED")) {
    define("ULICMS_GENERATED", ULICMS_CONTENT . "generated");
}

if (!defined("ULICMS_CONFIGURATIONS")) {
    define("ULICMS_CONFIGURATIONS", ULICMS_CONTENT . "/configurations/");
}

if (!is_dir(ULICMS_CACHE_BASE)) {
    mkdir(ULICMS_CACHE_BASE);
}
if (!is_dir(ULICMS_CACHE)) {
    mkdir(ULICMS_CACHE);
}
if (!is_dir(ULICMS_LOG)) {
    mkdir(ULICMS_LOG);
}
if (!is_dir(ULICMS_GENERATED)) {
    mkdir(ULICMS_GENERATED);
}

$htaccessForLogFolderSource = ULICMS_ROOT . "/lib/htaccess-deny-all.txt";
$htaccessLogFolderTarget = ULICMS_LOG . "/.htaccess";
if (!file_exists($htaccessLogFolderTarget)) {
    copy($htaccessForLogFolderSource, $htaccessLogFolderTarget);
}

Translation::init();

if (class_exists("Path")) {
    if (isset($config->exception_logging) && $config->exception_logging) {
        LoggerRegistry::register(
                "exception_log",
                new Logger(Path::resolve("ULICMS_LOG/exception_log"))
        );
    }
    if (isset($config->query_logging) && $config->query_logging) {
        LoggerRegistry::register(
                "sql_log",
                new Logger(Path::resolve("ULICMS_LOG/sql_log"))
        );
    }
    if (isset($config->phpmailer_logging) && $config->phpmailer_logging) {
        LoggerRegistry::register(
                "phpmailer_log",
                new Logger(Path::resolve("ULICMS_LOG/phpmailer_log"))
        );
    }
}

function noPerms(): void {
    echo "<div class=\"alert alert-danger\">"
    . get_translation("no_permissions") . "</div>";
}

$db_socket = $config->db_socket ?? ini_get("mysqli.default_socket");
$db_port = $config->db_port ?? ini_get("mysqli.default_port");
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
    throw new ConnectionFailedException("Can't connect to Database.");
}

$path_to_installer = dirname(__FILE__) . "/installer/installer.php";

if (isset($config->dbmigrator_auto_migrate) && $config->dbmigrator_auto_migrate) {
    $additionalSql = is_array($config->dbmigrator_initial_sql_files) ?
            $config->dbmigrator_initial_sql_files : [];
    if (isCLI()) {
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
    throw new SqlException("<h1>Database "
                    . $config->db_database . " doesn't exist.</h1>");
}

if (!Settings::get("session_name")) {
    Settings::set("session_name", uniqid() . "_SESSION");
}

UliCMS\Utils\Session\sessionName(Settings::get("session_name"));

$useragent = Settings::get("useragent");

define(
        "ULICMS_USERAGENT",
        $useragent ?
                $useragent : "UliCMS Release " . cms_version()
);

@ini_set('user_agent', ULICMS_USERAGENT);

if (!Settings::get("hide_meta_generator")) {
    @send_header('X-Powered-By: UliCMS Release ' . cms_version());
}

$cache_period = Settings::get("cache_period");

// by Check if the cache expiry is set.
// if not initialize setting with default value
if ($cache_period === null) {
    Settings::set("cache_period", (string) ONE_DAY_IN_SECONDS);
    define("CACHE_PERIOD", ONE_DAY_IN_SECONDS);
} else {
    define("CACHE_PERIOD", $cache_period);
}

date_default_timezone_set(Settings::get("timezone"));

if (isset($_GET["output_stylesheets"])) {
    getCombinedStylesheets();
}

$locale = Settings::get("locale");
if ($locale) {
    $locale = splitAndTrim($locale);
    array_unshift($locale, LC_ALL);
    @call_user_func_array("setlocale", $locale);
}

$session_timeout = 60 * intval(Settings::get("session_timeout"));

// Session abgelaufen
if (isset($_SESSION["session_begin"])) {
    if (time() - $_SESSION["session_begin"] > $session_timeout) {
        UliCMS\Utils\Session\sessionDestroy();
        send_header("Location: ./");
        exit();
    } else {
        $_SESSION["session_begin"] = time();
    }
}

register_shutdown_function(
        function () {
            do_event("shutdown");

            $cfg = new CMSConfig();
            if (isset($cfg->show_render_time) && $cfg->show_render_time && !Request::isAjaxRequest()) {
                echo "\n\n<!--" . (microtime(true) - START_TIME) . "-->";
            }
            if (isset($cfg->dbmigrator_drop_database_on_shutdown) && $cfg->dbmigrator_drop_database_on_shutdown) {
                if (isCLI()) {
                    Database::setEchoQueries(true);
                }
                Database::dropSchema($cfg->db_database);
                Database::setEchoQueries(false);
            }
        }
);

$defaultMenu = isset($config->default_menu) && !empty($config->default_menu) ?
        $config->default_menu : 'not_in_menu';
define("DEFAULT_MENU", $defaultMenu);

$defaultContentType = isset($config->default_content_type) && !empty($config->default_menu) ?
        $config->default_content_type : 'page';
define("DEFAULT_CONTENT_TYPE", $defaultContentType);

$enforce_https = Settings::get("enforce_https");

if (!is_ssl() && $enforce_https) {
    send_header("Location: https://" . $_SERVER["HTTP_HOST"] .
            $_SERVER["REQUEST_URI"]);
    exit();
}

$moduleManager = new ModuleManager();
Vars::set("disabledModules", $moduleManager->getDisabledModuleNames());

ModelRegistry::loadModuleModels();

TypeMapper::loadMapping();
HelperRegistry::loadModuleHelpers();
ControllerRegistry::loadModuleControllers();

do_event("before_init");
do_event("init");
do_event("after_init");

DefaultContentTypes::initTypes();
