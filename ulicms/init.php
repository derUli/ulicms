<?php
use UliCMS\Exceptions\AccessDeniedException;

// root directory of UliCMS
if (! defined("ULICMS_ROOT")) {
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

include_once dirname(__file__) . "/api.php";

/*
 * Diese Datei initalisiert das System
 */

// load composer packages
$composerAutoloadFile = dirname(__FILE__) . "/vendor/autoload.php";

if (is_file($composerAutoloadFile)) {
    include_once $composerAutoloadFile;
} else {
    throw new Exception("autoload.php not found. Please run \"./composer install\" to install dependecies.");
}

$classes_dir = ULICMS_ROOT . "/" . "classes";
@set_include_path(get_include_path() . PATH_SEPARATOR . $classes_dir);

// todo reorganize includes
include_once dirname(__file__) . "/lib/constants.php";
include_once dirname(__file__) . "/classes/objects/privacy/load.php";
include_once dirname(__file__) . "/lib/users_api.php";
include_once dirname(__file__) . "/lib/string_functions.php";
include_once dirname(__file__) . "/lib/network.php";
include_once dirname(__file__) . "/lib/settings.php";

include_once dirname(__file__) . "/classes/objects/abstract/load.php";
include_once dirname(__file__) . "/classes/objects/constants/load.php";
include_once dirname(__file__) . "/classes/objects/storages/load.php";
include_once dirname(__file__) . "/classes/objects/modules/load.php";
include_once dirname(__file__) . "/classes/objects/backend/load.php";
include_once dirname(__file__) . "/classes/objects/settings/load.php";
include_once dirname(__file__) . "/classes/objects/web/load.php";
include_once dirname(__file__) . "/classes/objects/content/Categories.php";
include_once dirname(__file__) . "/classes/objects/content/types/ContentType.php";
include_once dirname(__file__) . "/classes/objects/content/types/DefaultContentTypes.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/CustomField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/TextField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/MultilineTextField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/EmailField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/MonthField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/DatetimeField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/NumberField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/ColorField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/HtmlField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/SelectField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/CheckboxField.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/FileFile.php";
include_once dirname(__file__) . "/classes/objects/content/types/fields/FileImage.php";
include_once dirname(__file__) . "/classes/objects/pkg/load.php";
include_once dirname(__file__) . "/classes/helper/load.php";
include_once dirname(__file__) . "/classes/exceptions/load.php";
include_once dirname(__file__) . "/classes/objects/registry/load.php";
include_once dirname(__file__) . "/classes/objects/logging/load.php";
include_once dirname(__file__) . "/classes/objects/html/load.php";
include_once dirname(__file__) . "/classes/objects/SpellChecker.php";
include_once dirname(__file__) . "/classes/objects/content/TypeMapper.php";
include_once dirname(__file__) . "/lib/db_functions.php";
include_once dirname(__file__) . "/lib/files.php";
include_once dirname(__file__) . "/lib/mailer.php";
include_once dirname(__file__) . "/lib/file_get_contents_wrapper.php";
include_once dirname(__file__) . "/lib/translation.php";
include_once dirname(__file__) . "/lib/html5_media.php";
include_once dirname(__file__) . "/classes/objects/database/load.php";
include_once dirname(__file__) . "/classes/objects/Template.php";
include_once dirname(__file__) . "/classes/objects/security/load.php";
include_once dirname(__file__) . "/classes/objects/files/load.php";
include_once dirname(__file__) . "/classes/objects/spam/load.php";
include_once dirname(__file__) . "/classes/objects/users/load.php";
include_once dirname(__file__) . "/classes/objects/localization/load.php";
include_once dirname(__file__) . "/classes/objects/content/CustomData.php";
include_once dirname(__file__) . "/classes/objects/content/Category.php";
include_once dirname(__file__) . "/classes/objects/content/PagePermissions.php";
include_once dirname(__file__) . "/classes/objects/content/Content.php";
include_once dirname(__file__) . "/classes/objects/content/Page.php";
include_once dirname(__file__) . "/classes/objects/content/Snippet.php";
include_once dirname(__file__) . "/classes/objects/content/Link.php";
include_once dirname(__file__) . "/classes/objects/content/Language_Link.php";
include_once dirname(__file__) . "/classes/objects/content/Language.php";
include_once dirname(__file__) . "/classes/objects/content/Node.php";
include_once dirname(__file__) . "/classes/objects/content/List_Data.php";
include_once dirname(__file__) . "/classes/objects/content/Content_List.php";
include_once dirname(__file__) . "/classes/objects/content/Module_Page.php";
include_once dirname(__file__) . "/classes/objects/content/Video_Page.php";
include_once dirname(__file__) . "/classes/objects/content/Audio_Page.php";
include_once dirname(__file__) . "/classes/objects/content/Image_Page.php";
include_once dirname(__file__) . "/classes/objects/content/Banner.php";
include_once dirname(__file__) . "/classes/objects/content/Banners.php";
include_once dirname(__file__) . "/classes/objects/content/Article.php";
include_once dirname(__file__) . "/classes/objects/content/Comment.php";
include_once dirname(__file__) . "/classes/objects/content/ContentFactory.php";
include_once dirname(__file__) . "/classes/objects/content/CustomFields.php";
include_once dirname(__file__) . "/classes/objects/content/Results.php";
include_once dirname(__file__) . "/classes/objects/media/load.php";
include_once dirname(__file__) . "/UliCMSVersion.php";

$mobile_detect_as_module = dirname(__file__) . "/content/modules/Mobile_Detect/Mobile_Detect.php";
if (is_file($mobile_detect_as_module)) {
    include_once $mobile_detect_as_module;
}

function exception_handler($exception)
{
    if (! defined("EXCEPTION_OCCURRED")) {
        define("EXCEPTION_OCCURRED", true);
    }
    $error = nl2br(htmlspecialchars($exception));
    
    // FIXME: what if there is no config class?
    $cfg = class_exists("CMSConfig") ? new CMSConfig() : null;
    // TODO: useful error message if $debug is disabled
    // Log exception into a text file
    $message = is_true($cfg->debug) ? $exception : "An error occurred! See exception_log for details. 😞";
    
    $logger = LoggerRegistry::get("exception_log");
    if ($logger) {
        $logger->error($exception);
    }
    $httpStatus = $exception instanceof AccessDeniedException ? HttpStatusCode::FORBIDDEN : HttpStatusCode::INTERNAL_SERVER_ERROR;
    if (function_exists("HTMLResult") and class_exists("Template") and ! headers_sent() and function_exists("get_theme")) {
        ViewBag::set("exception", $message);
        HTMLResult(Template::executeDefaultOrOwnTemplate("exception.php"), $httpStatus);
    }
    if (function_exists("HTMLResult") and ! headers_sent()) {
        HTMLResult($message, $httpStatus);
    } else {
        echo "{$message}\n";
    }
}

// if config exists require_config else redirect to installer
$path_to_config = dirname(__file__) . "/CMSConfig.php";

// load config file
if (is_file($path_to_config)) {
    require_once $path_to_config;
} else if (is_dir("installer")) {
    header("Location: installer/");
    exit();
} else {
    die("Can't include CMSConfig.php. Starting installer failed, too.");
}

if (php_sapi_name() != "cli") {
    set_exception_handler('exception_handler');
}

// Backwards compatiblity for modules using the old config class name
if (class_exists("CMSConfig") and ! class_exists("config")) {
    class_alias("CMSConfig", "config");
}

global $config;
$config = new CMSConfig();

// IF ULICMS_DEBUG is defined then display all errors except E_NOTICE,
// else use default error_reporting from php.ini
if ((defined("ULICMS_DEBUG") and ULICMS_DEBUG) or (isset($config->debug) and $config->debug)) {
    error_reporting(E_ALL ^ E_NOTICE);
} else {
    error_reporting(0);
}

// UliCMS has support to define an alternative root folder
// to seperate it's core files from variable data such as modules and media
// this enables us to use stuff like Docker containers where data gets lost
// after stopping the container
if (isset($config->data_storage_root) and ! is_null($config->data_storage_root)) {
    define("ULICMS_DATA_STORAGE_ROOT", $config->data_storage_root);
} else {
    define("ULICMS_DATA_STORAGE_ROOT", ULICMS_ROOT);
}

// this enables us to set an base url for statis ressources such as images
// stored in ULICMS_DATA_STORAGE_ROOT
if (isset($config->data_storage_url) and ! is_null($config->data_storage_url)) {
    define("ULICMS_DATA_STORAGE_URL", $config->data_storage_url);
}

include_once dirname(__file__) . "/classes/creators/load.php";

if (! defined("ULICMS_TMP")) {
    define("ULICMS_TMP", ULICMS_DATA_STORAGE_ROOT . "/content/tmp/");
}

if (! is_dir(ULICMS_TMP)) {
    mkdir(ULICMS_TMP);
}

if (! defined("ULICMS_CACHE")) {
    define("ULICMS_CACHE", ULICMS_DATA_STORAGE_ROOT . "/content/cache/");
}
if (! defined("ULICMS_LOG")) {
    define("ULICMS_LOG", ULICMS_DATA_STORAGE_ROOT . "/content/log/");
}
if (! defined("ULICMS_CONTENT")) {
    define("ULICMS_CONTENT", ULICMS_DATA_STORAGE_ROOT . "/content/");
}

if (! defined("ULICMS_CONFIGURATIONS")) {
    define("ULICMS_CONFIGURATIONS", ULICMS_CONTENT . "/configurations/");
}
if (! is_dir(ULICMS_CACHE)) {
    mkdir(ULICMS_CACHE);
}
if (! is_dir(ULICMS_LOG)) {
    mkdir(ULICMS_LOG);
}

$htaccessForLogFolderSource = ULICMS_ROOT . "/lib/htaccess-deny-all.txt";
$htaccessLogFolderTarget = ULICMS_LOG . "/.htaccess";
if (! is_file($htaccessLogFolderTarget)) {
    copy($htaccessForLogFolderSource, $htaccessLogFolderTarget);
}

// umask setzen
// Die umask legt die Standarddateirechte für neue Dateien auf Unix Systemen fest
// Die Variable $umask sollte nur gesetzt werden, sofern es zu Berechtigungsproblemen bei durch UliCMS generierten Dateien kommt.
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
    LoggerRegistry::register("exception_log", new Logger(Path::resolve("ULICMS_LOG/exception_log")));
    
    if (is_true($config->query_logging)) {
        LoggerRegistry::register("sql_log", new Logger(Path::resolve("ULICMS_LOG/sql_log")));
    }
    if (is_true($config->phpmailer_logging)) {
        LoggerRegistry::register("phpmailer_log", new Logger(Path::resolve("ULICMS_LOG/phpmailer_log")));
    }
    if (is_true($config->audit_log)) {
        LoggerRegistry::register("audit_log", new Logger(Path::resolve("ULICMS_LOG/audit_log")));
    }
}
include_once dirname(__file__) . "/lib/minify.php";

// define Constants
define('CR', "\r"); // carriage return; Mac
define('LF', "\n"); // line feed; Unix
define('CRLF', "\r\n"); // carriage return and line feed; Windows
define('BR', '<br />' . LF); // HTML Break
define("ONE_DAY_IN_SECONDS", 60 * 60 * 24);

global $actions;
$actions = array();

function noPerms()
{
    echo "<p class=\"ulicms_error\">" . get_translation("no_permissions") . "</p>";
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

function is_in_include_path($find)
{
    $paths = explode(PATH_SEPARATOR, get_include_path());
    $found = false;
    foreach ($paths as $p) {
        $fullname = $p . DIRECTORY_SEPARATOR . $find;
        if (is_file($fullname)) {
            $found = $fullname;
            break;
        }
    }
}

global $config;
$config = new CMSConfig();

$db_socket = isset($config->db_socket) ? $config->db_socket : ini_get("mysqli.default_socket");

$db_port = isset($config->db_port) ? $config->db_port : ini_get("mysqli.default_port");

@$connection = Database::connect($config->db_server, $config->db_user, $config->db_password, $db_port, $db_socket);

if ($connection === false) {
    throw new Exception("<h1>Can't connect to Database.</h1>");
}

$path_to_installer = dirname(__file__) . "/installer/installer.php";

$select = Database::select($config->db_database);

if (! $select) {
    throw new Exception("<h1>Database " . $config->db_database . " doesn't exist.</h1>");
}

if (is_true($cfg->preload_all_settings)) {
    Settings::preloadAll();
}

if (! Settings::get("session_name")) {
    Settings::set("session_name", uniqid() . "_SESSION");
}

session_name(Settings::get("session_name"));

$useragent = Settings::get("useragent");

if ($useragent) {
    define("ULICMS_USERAGENT", $useragent);
} else {
    define("ULICMS_USERAGENT", "UliCMS Release " . cms_version());
}

@ini_set('user_agent', ULICMS_USERAGENT);

if (! Settings::get("hide_meta_generator")) {
    @header('X-Powered-By: UliCMS Release ' . cms_version());
}

$memory_limit = Settings::get("memory_limit");

if ($memory_limit !== false) {
    @ini_set('memory_limit', $memory_limit);
}

if (isset($config->log_requests) and $config->log_requests == true) {
    $log_ip = Settings::get("log_ip");
    log_request($log_ip);
}

$cache_period = Settings::get("cache_period");

// Prüfen ob Cache Gültigkeitsdauer gesetzt ist.
// Ansonsten auf Standardwert setzen
if ($cache_period === false) {
    setconfig("cache_period", ONE_DAY_IN_SECONDS);
    define("CACHE_PERIOD", ONE_DAY_IN_SECONDS);
} else {
    define("CACHE_PERIOD", $cache_period);
}

date_default_timezone_set(Settings::get("timezone"));

if (isset($_GET["output_scripts"])) {
    getCombinedScripts();
} else if (isset($_GET["output_stylesheets"])) {
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
        session_destroy();
        header("Location: ./");
        exit();
    } else {
        $_SESSION["session_begin"] = time();
    }
}

function shutdown_function()
{
    // don't execute shutdown hook on kcfinder page (media)
    // since the "Path" class has a naming conflict with the same named
    // class of KCFinder
    do_event("shutdown");
    
    $cfg = new CMSConfig();
    if (is_true($cfg->show_render_time) and ! Request::isAjaxRequest()) {
        echo "\n\n<!--" . (microtime(true) - START_TIME) . "-->";
    }
}

register_shutdown_function("shutdown_function");

$enforce_https = Settings::get("enforce_https");

if (! is_ssl() and $enforce_https !== false) {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

$moduleManager = new ModuleManager();
Vars::set("disabledModules", $moduleManager->getDisabledModuleNames());
// don't load module stuff on kcfinder page (media)
// since the "Path" class has a naming conflict with the same named
// class of KCFinder
ModelRegistry::loadModuleModels();
TypeMapper::loadMapping();
HelperRegistry::loadModuleHelpers();
ControllerRegistry::loadModuleControllers();

do_event("before_init");
do_event("init");
do_event("after_init");

$version = new UliCMSVersion();
if (! defined("UPDATE_CHECK_URL")) {
    define("UPDATE_CHECK_URL", "https://update.ulicms.de/?v=" . urlencode(implode(".", $version->getInternalVersion())) . "&update=" . urlencode($version->getUpdate()));
}

$pkg = new PackageManager();
$installed_patches = $pkg->getInstalledPatchNames();
$installed_patches = implode(";", $installed_patches);

if (! defined("PATCH_CHECK_URL")) {
    define("PATCH_CHECK_URL", "https://patches.ulicms.de/?v=" . urlencode(implode(".", $version->getInternalVersion())) . "&installed_patches=" . urlencode($installed_patches));
}


