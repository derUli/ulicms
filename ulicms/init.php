<?php

// use this constant at the end
// of the page load procedure to measure site performance
define("START_TIME", microtime(true));

// 24 Stunden in Sekunden
define("ONE_DAY_IN_SECONDS", 60 * 60 * 24);

// root directory of UliCMS
define("ULICMS_ROOT", dirname(__FILE__));

// Class dir for autoload
define('CLASSDIR', ULICMS_ROOT . '/classes');

// Temp Directory
define("ULICMS_TMP", ULICMS_ROOT . "/content/tmp/");

// Cache Directory
define("ULICMS_CACHE", ULICMS_ROOT . "/content/cache/");

// Log Directory
define("ULICMS_LOG", ULICMS_ROOT . "/content/log/");

// Content Directory
define("ULICMS_CONTENT", ULICMS_ROOT . "/content/");

// Generated Directory
define("ULICMS_GENERATED", ULICMS_CONTENT . "generated");

// Configuration Directory
define("ULICMS_CONFIGURATIONS", ULICMS_CONTENT . "/configurations/");

// Autoload classes
spl_autoload_register(function ($class) {
    $file = CLASSDIR . "/" . str_ireplace("UliCMS\\", "", $class) . ".php";
    $file = str_replace("\\", "/", $file);

    if (file_exists($file)) {
        require $file;
    }
});

use UliCMS\Exceptions\AccessDeniedException;
use UliCMS\Exceptions\ConnectionFailedException;
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Exceptions\SqlException;
use UliCMS\Constants\AuditLog;
use UliCMS\Constants\HttpStatusCode;
use UliCMS\Registries\HelperRegistry;
use UliCMS\Registries\LoggerRegistry;
use UliCMS\Models\Content\TypeMapper;
use UliCMS\Packages\PatchManager;
use UliCMS\Registries\ModelRegistry;
use UliCMS\Logging\Logger;
use UliCMS\Packages\Modules\ModuleManager;
use UliCMS\Utils\Session;
use UliCMS\Storages\Vars;
use UliCMS\Localization\Translation;

// load composer packages
$composerAutoloadFile = ULICMS_ROOT . "/vendor/autoload.php";

if (file_exists($composerAutoloadFile)) {
    require_once $composerAutoloadFile;
} else {
    throw new FileNotFoundException(
                    "autoload.php not found. "
                    . "Please run \"./composer install\" to install dependecies."
    );
}

require_once ULICMS_ROOT . "/classes/Helpers/load.php";
require_once ULICMS_ROOT . "/classes/objects/content/CustomData.php";
require_once ULICMS_ROOT . "/classes/objects/content/Content.php";
require_once ULICMS_ROOT . "/classes/objects/content/Page.php";
require_once ULICMS_ROOT . "/classes/objects/content/Snippet.php";
require_once ULICMS_ROOT . "/classes/objects/content/Link.php";
require_once ULICMS_ROOT . "/classes/objects/content/Language_Link.php";
require_once ULICMS_ROOT . "/classes/objects/content/Node.php";
require_once ULICMS_ROOT . "/classes/objects/content/List_Data.php";
require_once ULICMS_ROOT . "/classes/objects/content/Content_List.php";
require_once ULICMS_ROOT . "/classes/objects/content/Module_Page.php";
require_once ULICMS_ROOT . "/classes/objects/content/Video_Page.php";
require_once ULICMS_ROOT . "/classes/objects/content/Audio_Page.php";
require_once ULICMS_ROOT . "/classes/objects/content/Image_Page.php";
require_once ULICMS_ROOT . "/classes/objects/content/Article.php";
require_once ULICMS_ROOT . "/classes/objects/content/ContentFactory.php";
require_once ULICMS_ROOT . "/classes/objects/content/CustomFields.php";

$mobile_detect_as_module = ULICMS_ROOT .
        "/content/modules/Mobile_Detect/Mobile_Detect.php";
if (file_exists($mobile_detect_as_module)) {
    require_once $mobile_detect_as_module;
}

function exception_handler($exception) {
    if (!defined("EXCEPTION_OCCURRED")) {
        define("EXCEPTION_OCCURRED", true);
    }

    // FIXME: what if there is no config class?
    $cfg = class_exists("CMSConfig") ? new CMSConfig() : null;

    $message = isset($cfg->debug) && $cfg->debug ?
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
    if (function_exists("HTMLResult") && !headers_sent()) {
        HTMLResult($message, $httpStatus);
    } else {
        echo "{$message}\n";
    }
}

// if config exists require_config else redirect to installer
$path_to_config = ULICMS_ROOT . "/CMSConfig.php";

Vars::set("http_headers", []);

// load config file
if (file_exists($path_to_config)) {
    require_once $path_to_config;
} elseif (is_dir("installer")) {
    send_header("Location: installer/");
    exit();
} else {
    throw new Exception("Can't require CMSConfig.php. Starting installer failed, too.");
}

if (php_sapi_name() != "cli") {
    set_exception_handler('exception_handler');
}

$config = new CMSConfig();

// IF ULICMS_DEBUG is defined then display all errors except E_NOTICE,
// else disable error_reporting from php.ini
if ((defined("ULICMS_DEBUG") && ULICMS_DEBUG) || (isset($config->debug) && $config->debug)) {
    error_reporting(E_ALL ^ E_NOTICE);
} else {
    error_reporting(0);
}

if (!is_dir(ULICMS_TMP)) {
    mkdir(ULICMS_TMP);
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

// umask setzen
// Die umask legt die Standarddateirechte für neue Dateien auf
// Unix Systemen fest.
// Die Variable $umask sollte nur gesetzt werden, sofern es zu
// Berechtigungsproblemen bei durch UliCMS generierten Dateien kommt.
// umask lässt sich wie folgt berechnen
// 0777 - X = gewünschte Berechtigung
// X ist die umask
// Eine umask von 0022 erzeugt z.B. Ordner mit chmod 0755 und Dateien mit chmod 0655
if (isset($config->umask)) {
    umask($config->umask);
}

// memory_limit setzen
if (isset($config->memory_limit)) {
    @ini_set("memory_limit", $config->memory_limit);
}

Translation::init();

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
if (isset($config->audit_log) && $config->audit_log) {
    LoggerRegistry::register(
            "audit_log",
            new Logger(Path::resolve("ULICMS_LOG/audit_log"))
    );
}

function noPerms() {
    echo "<div class=\"alert alert-danger\">"
    . get_translation("no_permissions") . "</div>";
    $logger = LoggerRegistry::get("audit_log");
    if ($logger) {
        $userId = get_user_id();
        $name = AuditLog::UNKNOWN;
        if ($userId) {
            $user = getUserById($userId);
            $name = $user["username"];
        }
        $url = get_request_uri();
        $logger->error("User $name - No Permission on URL ($url)");
    }
    return false;
}

$db_socket = isset($config->db_socket) ?
        $config->db_socket : ini_get("mysqli.default_socket");

$db_port = isset($config->db_port) ?
        $config->db_port : ini_get("mysqli.default_port");
$db_strict_mode = isset($config->db_strict_mode) ?
        boolval($config->db_strict_mode) : false;

// Seit PHP ist der Default-Wert MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
// TODO: Datenbank-Code fixen, damit auch ohne diese Zeile alles funktioniert
mysqli_report(MYSQLI_REPORT_OFF);

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

$path_to_installer = ULICMS_ROOT . "/installer/installer.php";

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

Session::sessionName(Settings::get("session_name"));

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

$memory_limit = Settings::get("memory_limit");

if ($memory_limit) {
    @ini_set('memory_limit', $memory_limit);
}

$cache_period = Settings::get("cache_period");

// by Check if the cache expiry is set.
// if not initialize setting with default value
if ($cache_period === null) {
    Settings::set("cache_period", strval(ONE_DAY_IN_SECONDS));
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
        Session::sessionDestroy();
        send_header("Location: ./");
        exit();
    } else {
        $_SESSION["session_begin"] = time();
    }
}

function shutdown_function() {
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

register_shutdown_function("shutdown_function");

$patchManager = new PatchManager();
$installed_patches = $patchManager->getInstalledPatchNames();
$installed_patches = implode(";", $installed_patches);
$version = new UliCMSVersion();

define("PATCH_CHECK_URL", "https://patches.ulicms.de/?v=" .
        urlencode(implode(".", $version->getInternalVersion())) . "&installed_patches=" . urlencode($installed_patches));

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
require_once ULICMS_ROOT . "/lib/templating.php";

do_event("before_init");
do_event("init");
do_event("after_init");
