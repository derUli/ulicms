<?php
require_once dirname(__file__) . "/classes/exceptions/load.php";

use UliCMS\Exceptions\AccessDeniedException;
use UliCMS\Exceptions\ConnectionFailedException;
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Exceptions\SqlException;
use UliCMS\Constants\AuditLog;
use UliCMS\Registries\HelperRegistry;
use UliCMS\Models\Content\TypeMapper;
use UliCMS\Packages\PatchManager;

// root directory of UliCMS
if (!defined("ULICMS_ROOT")) {
    define("ULICMS_ROOT", dirname(__file__));
}

// this is kept for compatiblity reasons
define("DIRECTORY_SEPERATOR", DIRECTORY_SEPARATOR);

// shortcut for DIRECTORY_SEPARATOR
// however it's unnecessary to use these constansts
// since PHP normalizes all paths
// So just always use a forward slash
// Shortcut, but should not be used anymore
// Just use /
define("DIRSEP", DIRECTORY_SEPARATOR);

// use this constant at the end
// of the page load procedure to measure site performance
define("START_TIME", microtime(true));

/*
 * Diese Datei initalisiert das System
 */

// load composer packages
$composerAutoloadFile = dirname(__FILE__) . "/vendor/autoload.php";

if (file_exists($composerAutoloadFile)) {
    require_once $composerAutoloadFile;
} else {
    throw new FileNotFoundException(
        "autoload.php not found. "
            . "Please run \"./composer install\" to install dependecies."
    );
}

require_once dirname(__file__) . "/lib/load.php";
require_once dirname(__file__) . "/classes/objects/privacy/load.php";
require_once dirname(__file__) . "/classes/objects/abstract/load.php";
require_once dirname(__file__) . "/classes/objects/constants/load.php";
require_once dirname(__file__) . "/classes/objects/storages/load.php";
require_once dirname(__file__) . "/classes/objects/modules/load.php";
require_once dirname(__file__) . "/classes/objects/settings/load.php";
require_once dirname(__file__) . "/classes/objects/web/load.php";
require_once dirname(__file__) . "/classes/objects/content/Categories.php";
require_once dirname(__file__) . "/classes/objects/content/VCS.php";
require_once dirname(__file__) . "/classes/objects/content/types/ContentType.php";
require_once dirname(__file__) . "/classes/objects/content/types/DefaultContentTypes.php";
require_once dirname(__file__) . "/classes/objects/content/types/fields/load.php";

require_once dirname(__file__) . "/classes/objects/pkg/load.php";
require_once dirname(__file__) . "/classes/helpers/load.php";
require_once dirname(__file__) . "/classes/objects/registry/load.php";
require_once dirname(__file__) . "/classes/objects/logging/load.php";
require_once dirname(__file__) . "/classes/objects/html/load.php";
require_once dirname(__file__) . "/classes/objects/content/TypeMapper.php";
require_once dirname(__file__) . "/classes/objects/database/load.php";
require_once dirname(__file__) . "/classes/objects/security/load.php";
require_once dirname(__file__) . "/classes/objects/files/load.php";
require_once dirname(__file__) . "/classes/objects/spam/load.php";
require_once dirname(__file__) . "/classes/objects/users/load.php";
require_once dirname(__file__) . "/classes/objects/localization/load.php";
require_once dirname(__file__) . "/classes/objects/content/CustomData.php";
require_once dirname(__file__) . "/classes/objects/content/Category.php";
require_once dirname(__file__) . "/classes/objects/content/PagePermissions.php";
require_once dirname(__file__) . "/classes/objects/content/Content.php";
require_once dirname(__file__) . "/classes/objects/content/Page.php";
require_once dirname(__file__) . "/classes/objects/content/Snippet.php";
require_once dirname(__file__) . "/classes/objects/content/Link.php";
require_once dirname(__file__) . "/classes/objects/content/Language_Link.php";
require_once dirname(__file__) . "/classes/objects/content/Language.php";
require_once dirname(__file__) . "/classes/objects/content/Node.php";
require_once dirname(__file__) . "/classes/objects/content/List_Data.php";
require_once dirname(__file__) . "/classes/objects/content/Content_List.php";
require_once dirname(__file__) . "/classes/objects/content/Module_Page.php";
require_once dirname(__file__) . "/classes/objects/content/Video_Page.php";
require_once dirname(__file__) . "/classes/objects/content/Audio_Page.php";
require_once dirname(__file__) . "/classes/objects/content/Image_Page.php";
require_once dirname(__file__) . "/classes/objects/content/Banner.php";
require_once dirname(__file__) . "/classes/objects/content/Banners.php";
require_once dirname(__file__) . "/classes/objects/content/Article.php";
require_once dirname(__file__) . "/classes/objects/content/Comment.php";
require_once dirname(__file__) . "/classes/objects/content/ContentFactory.php";
require_once dirname(__file__) . "/classes/objects/content/CustomFields.php";
require_once dirname(__file__) . "/classes/objects/content/Results.php";
require_once dirname(__file__) . "/classes/objects/media/load.php";
require_once dirname(__file__) . "/classes/objects/backend/load.php";

require_once dirname(__file__) . "/UliCMSVersion.php";

$mobile_detect_as_module = dirname(__file__) .
        "/content/modules/Mobile_Detect/Mobile_Detect.php";
if (file_exists($mobile_detect_as_module)) {
    require_once $mobile_detect_as_module;
}

function exception_handler($exception)
{
    if (!defined("EXCEPTION_OCCURRED")) {
        define("EXCEPTION_OCCURRED", true);
    }

    // FIXME: what if there is no config class?
    $cfg = class_exists("CMSConfig") ? new CMSConfig() : null;

    $message = !is_null($cfg) && is_true($cfg->debug) ?
            $exception : "An error occurred! See exception_log for details. 😞";

    $logger = LoggerRegistry::get("exception_log");
    if ($logger) {
        $logger->error($exception);
    }
    $httpStatus = $exception instanceof AccessDeniedException ?
            HttpStatusCode::FORBIDDEN : HttpStatusCode::INTERNAL_SERVER_ERROR;
    if (function_exists("HTMLResult") and class_exists("Template")
            && !headers_sent() and function_exists("get_theme")) {
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
$path_to_config = dirname(__file__) . "/CMSConfig.php";

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

// Backwards compatiblity for modules using the old config class name
if (class_exists("CMSConfig") && !class_exists("config")) {
    class_alias("CMSConfig", "config");
}

global $config;
$config = new CMSConfig();

// IF ULICMS_DEBUG is defined then display all errors except E_NOTICE,
// else disable error_reporting from php.ini
if ((defined("ULICMS_DEBUG") and ULICMS_DEBUG)
        or (isset($config->debug) and $config->debug)) {
    error_reporting(E_ALL ^ E_NOTICE);
} else {
    error_reporting(0);
}

// UliCMS has support to define an alternative root folder
// to seperate it's core files from variable data such as modules and media
// this enables us to use stuff like Docker containers where data gets lost
// after stopping the container
if (isset($config->data_storage_root)
        && !is_null($config->data_storage_root)) {
    define("ULICMS_DATA_STORAGE_ROOT", $config->data_storage_root);
} else {
    define("ULICMS_DATA_STORAGE_ROOT", ULICMS_ROOT);
}

require_once dirname(__file__) . "/classes/renderers/load.php";

// this enables us to set an base url for statis ressources such as images
// stored in ULICMS_DATA_STORAGE_ROOT
if (isset($config->data_storage_url)
        && !is_null($config->data_storage_url)) {
    define("ULICMS_DATA_STORAGE_URL", $config->data_storage_url);
}

if (!defined("ULICMS_TMP")) {
    define("ULICMS_TMP", ULICMS_DATA_STORAGE_ROOT . "/content/tmp/");
}

if (!is_dir(ULICMS_TMP)) {
    mkdir(ULICMS_TMP);
}

if (!defined("ULICMS_CACHE")) {
    define("ULICMS_CACHE", ULICMS_DATA_STORAGE_ROOT . "/content/cache/");
}
if (!defined("ULICMS_LOG")) {
    define("ULICMS_LOG", ULICMS_DATA_STORAGE_ROOT . "/content/log/");
}
if (!defined("ULICMS_CONTENT")) {
    define("ULICMS_CONTENT", ULICMS_DATA_STORAGE_ROOT . "/content/");
}

if (!defined("ULICMS_GENERATED")) {
    define("ULICMS_GENERATED", ULICMS_CONTENT . "generated");
}

if (!defined("ULICMS_CONFIGURATIONS")) {
    define("ULICMS_CONFIGURATIONS", ULICMS_CONTENT . "/configurations/");
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

if (class_exists("Path")) {
    if (isset($config->exception_logging) and is_true($config->exception_logging)) {
        LoggerRegistry::register(
            "exception_log",
            new Logger(Path::resolve("ULICMS_LOG/exception_log"))
        );
    }
    if (isset($config->query_logging) and is_true($config->query_logging)) {
        LoggerRegistry::register(
            "sql_log",
            new Logger(Path::resolve("ULICMS_LOG/sql_log"))
        );
    }
    if (isset($config->phpmailer_logging) and is_true($config->phpmailer_logging)) {
        LoggerRegistry::register(
            "phpmailer_log",
            new Logger(Path::resolve("ULICMS_LOG/phpmailer_log"))
        );
    }
    if (isset($config->audit_log) and is_true($config->audit_log)) {
        LoggerRegistry::register(
            "audit_log",
            new Logger(Path::resolve("ULICMS_LOG/audit_log"))
        );
    }
}

// define Constants
define('CR', "\r"); // carriage return; Mac
define('LF', "\n"); // line feed; Unix
define('CRLF', "\r\n"); // carriage return and line feed; Windows
define('BR', '<br />' . LF); // HTML Break
define("ONE_DAY_IN_SECONDS", 60 * 60 * 24);

function noPerms()
{
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

$path_to_installer = dirname(__file__) . "/installer/installer.php";

if (isset($config->dbmigrator_auto_migrate) and is_true($config->dbmigrator_auto_migrate)) {
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
        UliCMS\Utils\Session\sessionDestroy();
        send_header("Location: ./");
        exit();
    } else {
        $_SESSION["session_begin"] = time();
    }
}

function shutdown_function()
{
    do_event("shutdown");

    $cfg = new CMSConfig();
    if (isset($cfg->show_render_time) and is_true($cfg->show_render_time) && !Request::isAjaxRequest()) {
        echo "\n\n<!--" . (microtime(true) - START_TIME) . "-->";
    }
    if (isset($cfg->dbmigrator_drop_database_on_shutdown) and is_true($cfg->dbmigrator_drop_database_on_shutdown)) {
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

if (!is_ssl() and $enforce_https) {
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
require_once dirname(__file__) . "/lib/templating.php";

do_event("before_init");
do_event("init");
do_event("after_init");
