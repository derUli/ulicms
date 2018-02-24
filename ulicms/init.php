<?php
/*
 * Diese Datei initalisiert das System
 */
$composerAutoloadFile = dirname ( __FILE__ ) . "/vendor/autoload.php";

if (file_exists ( $composerAutoloadFile )) {
	include_once $composerAutoloadFile;
} else {
	throw new Exception ( "autoload.php not found. Please run \"./composer install\” to install dependecies." );
}
// root directory of UliCMS
if (! defined ( "ULICMS_ROOT" )) {
	define ( "ULICMS_ROOT", dirname ( __file__ ) );
}
function uimport($class) {
	$path = str_replace ( "\\", "/", ULICMS_ROOT ) . "/" . $class . ".php";
	return include_once $path;
}
function idefine($key, $value) {
	if (! defined ( $key )) {
		define ( $key, $value );
	}
}
function faster_in_array($needle, $haystack) {
	$flipped = array_flip ( $haystack );
	return isset ( $flipped [$needle] );
}

// UliCMS verweigert den Betrieb mit aktivierten Register Globals
if (ini_get ( 'register_globals' ) === '1') {
	die ( 'SECURITY WARNING: "Register Globals" feature is enabled! UliCMS refuses to run with enabled "Register Globals"!' );
}

$os = PHP_OS;
switch ($os) {
	case "Linux" :
		define ( "DIRECTORY_SEPERATOR", "/" );
		break;
	case "Windows" :
		define ( "DIRECTORY_SEPERATOR", "\\" );
		break;
	default :
		define ( "DIRECTORY_SEPERATOR", "/" );
		break;
}

$classes_dir = ULICMS_ROOT . DIRECTORY_SEPERATOR . "classes";
@set_include_path ( get_include_path () . PATH_SEPARATOR . $classes_dir );

if (! defined ( "ULICMS_TMP" )) {
	define ( "ULICMS_TMP", dirname ( __file__ ) . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "tmp" . DIRECTORY_SEPERATOR );
}

if (! file_exists ( ULICMS_TMP )) {
	mkdir ( ULICMS_TMP );
}

if (! defined ( "ULICMS_CACHE" )) {
	define ( "ULICMS_CACHE", dirname ( __file__ ) . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "cache" . DIRECTORY_SEPERATOR );
}
if (! defined ( "ULICMS_LOG" )) {
	define ( "ULICMS_LOG", dirname ( __file__ ) . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "log" . DIRECTORY_SEPERATOR );
}
if (! defined ( "ULICMS_CONTENT" )) {
	define ( "ULICMS_CONTENT", dirname ( __file__ ) . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR );
}
if (! file_exists ( ULICMS_CACHE )) {
	mkdir ( ULICMS_CACHE );
}
if (! file_exists ( ULICMS_LOG )) {
	mkdir ( ULICMS_LOG );
}

$htaccessForLogFolderSource = ULICMS_ROOT . "/lib/htaccess-deny-all.txt";
$htaccessLogFolderTarget = ULICMS_LOG . "/.htaccess";
if (! file_exists ( $htaccessLogFolderTarget )) {
	copy ( $htaccessForLogFolderSource, $htaccessLogFolderTarget );
}

include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "constants.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "users_api.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "network.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "settings.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "constants" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "storages" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "modules" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "backend" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "settings" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "web" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "antispam-features.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Categories.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "ContentType.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "DefaultContentTypes.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "CustomField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "TextField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "MultilineTextField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "EmailField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "MonthField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "DatetimeField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "NumberField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "ColorField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "HtmlField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "SelectField.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "types" . DIRECTORY_SEPERATOR . "fields" . DIRECTORY_SEPERATOR . "CheckboxField.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "pkg" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "load.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "exceptions" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "registry" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "files" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "logging" . DIRECTORY_SEPERATOR . "load.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "html" . DIRECTORY_SEPERATOR . "load.php";

$mobile_detect_as_module = dirname ( __file__ ) . "/content/modules/Mobile_Detect/Mobile_Detect.php";
if (file_exists ( $mobile_detect_as_module )) {
	include_once $mobile_detect_as_module;
}

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "version.php";
function exception_handler($exception) {
	if (! defined ( "EXCEPTION_OCCURRED" )) {
		define ( "EXCEPTION_OCCURRED", true );
	}
	$error = nl2br ( htmlspecialchars ( $exception ) );
	
	$cfg = class_exists ( "config" ) ? new config () : null;
	// TODO: useful error message if $debug is disabled
	// Log exception into a text file
	$message = is_true ( $cfg->debug ) ? $exception : "Something happened! 😞";
	
	$logger = LoggerRegistry::get ( "exception_log" );
	if ($logger) {
		$logger->error ( $exception );
	}
	
	if (function_exists ( "HTMLResult" ) and class_exists ( "Template" ) and ! headers_sent () and function_exists ( "get_theme" )) {
		ViewBag::set ( "exception", $message );
		HTMLResult ( Template::executeDefaultOrOwnTemplate ( "exception.php" ), 500 );
	}
	echo "{$message}\n";
}

if (php_sapi_name () != "cli") {
	set_exception_handler ( 'exception_handler' );
}

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "creators" . DIRECTORY_SEPERATOR . "load.php";

// if config exists require_config else redirect to installer
$path_to_config = dirname ( __file__ ) . DIRECTORY_SEPERATOR . "cms-config.php";

if (file_exists ( $path_to_config )) {
	require_once $path_to_config;
} else if (is_dir ( "installer" )) {
	header ( "Location: installer/" );
	exit ();
} else {
	throw new Exception ( "Can't include cms-config.php. Starting installer failed, too." );
}

global $config;
$config = new config ();

// IF ULICMS_DEBUG is defined then display all errors except E_NOTICE,
// else use default error_reporting from php.ini
if ((defined ( "ULICMS_DEBUG" ) and ULICMS_DEBUG) or (isset ( $config->debug ) and $config->debug)) {
	error_reporting ( E_ALL ^ E_NOTICE );
} else {
	error_reporting ( 0 );
}

// umask setzen
// Die umask legt die Standarddateirechte für neue Dateien auf Unix Systemen fest
// Die Variable $umask sollte nur gesetzt werden, sofern es zu Berechtigungsproblemen bei durch UliCMS generierten Dateien kommt.
// umask lässt sich wie folgt berechnen
// 0777 - X = gewünschte Berechtigung
// X ist die umask
// Eine umask von 0022 erzeugt z.B. Ordner mit chmod 0755 und Dateien mit chmod 0655
if (isset ( $config->umask )) {
	umask ( $config->umask );
}

// memory_limit setzen
if (isset ( $config->memory_limit )) {
	@ini_set ( "memory_limit", $config->memory_limit );
}
require_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "api.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "abstract" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "SpellChecker.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "TypeMapper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "db_functions.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "files.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "mailer.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "file_get_contents_wrapper.php";
require_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "translation.php";
require_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "html5_media.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "database" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "Template.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "security" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "files" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "users" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "localization" . DIRECTORY_SEPERATOR . "load.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "CustomData.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Category.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Content.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Snippet.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Link.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Language.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Node.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "List_Data.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Content_List.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Module_Page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Video_Page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Audio_Page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Image_Page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Banner.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Banners.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Article.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Comment.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "ContentFactory.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "CustomFields.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Language.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "Results.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "media" . DIRECTORY_SEPERATOR . "load.php";

Translation::init ();

if (class_exists ( "Path" )) {
	LoggerRegistry::register ( "exception_log", new Logger ( Path::resolve ( "ULICMS_LOG/exception_log" ) ) );
	
	if (is_true ( $config->query_logging )) {
		LoggerRegistry::register ( "sql_log", new Logger ( Path::resolve ( "ULICMS_LOG/sql_log" ) ) );
	}
}

require_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib/minify.php";

// define Constants
define ( 'CR', "\r" ); // carriage return; Mac
define ( 'LF', "\n" ); // line feed; Unix
define ( 'CRLF', "\r\n" ); // carriage return and line feed; Windows
define ( 'BR', '<br />' . LF ); // HTML Break
define ( "ONE_DAY_IN_SECONDS", 60 * 60 * 24 );

global $actions;
$actions = array ();
function noperms() {
	echo "<p class=\"ulicms_error\">" . get_translation ( "no_permissions" ) . "</p>";
	return false;
}
function startsWith($haystack, $needle, $case = true) {
	if ($case) {
		return (strcmp ( substr ( $haystack, 0, strlen ( $needle ) ), $needle ) === 0);
	}
	return (strcasecmp ( substr ( $haystack, 0, strlen ( $needle ) ), $needle ) === 0);
}
function endsWith($haystack, $needle, $case = true) {
	if ($case) {
		return (strcmp ( substr ( $haystack, strlen ( $haystack ) - strlen ( $needle ) ), $needle ) === 0);
	}
	return (strcasecmp ( substr ( $haystack, strlen ( $haystack ) - strlen ( $needle ) ), $needle ) === 0);
}
function is_in_include_path($find) {
	$paths = explode ( PATH_SEPARATOR, get_include_path () );
	$found = false;
	foreach ( $paths as $p ) {
		$fullname = $p . DIRECTORY_SEPARATOR . $find;
		if (is_file ( $fullname )) {
			$found = $fullname;
			break;
		}
	}
}

global $config;
$config = new config ();

$db_socket = isset ( $config->db_socket ) ? $config->db_socket : ini_get ( "mysqli.default_socket" );

$db_port = isset ( $config->db_port ) ? $config->db_port : ini_get ( "mysqli.default_port" );

@$connection = Database::connect ( $config->db_server, $config->db_user, $config->db_password, $db_port, $db_socket );

if ($connection === false) {
	throw new Exception ( "<h1>Can't connect to Database.</h1>" );
}

$path_to_installer = dirname ( __file__ ) . DIRECTORY_SEPERATOR . "installer" . DIRECTORY_SEPERATOR . "installer.php";

$select = Database::select ( $config->db_database );

if (! $select) {
	throw new Exception ( "<h1>Database " . $config->db_database . " doesn't exist.</h1>" );
}

$useragent = Settings::get ( "useragent" );

if ($useragent) {
	define ( "ULICMS_USERAGENT", $useragent );
} else {
	define ( "ULICMS_USERAGENT", "UliCMS Release " . cms_version () );
}

@ini_set ( 'user_agent', ULICMS_USERAGENT );

if (! Settings::get ( "hide_meta_generator" )) {
	@header ( 'X-Powered-By: UliCMS Release ' . cms_version () );
}

$memory_limit = Settings::get ( "memory_limit" );

if ($memory_limit !== false) {
	@ini_set ( 'memory_limit', $memory_limit );
}

if (isset ( $config->log_requests ) and $config->log_requests == true) {
	$log_ip = Settings::get ( "log_ip" );
	log_request ( $log_ip );
}

$cache_period = Settings::get ( "cache_period" );

// Prüfen ob Cache Gültigkeitsdauer gesetzt ist.
// Ansonsten auf Standardwert setzen
if ($cache_period === false) {
	setconfig ( "cache_period", ONE_DAY_IN_SECONDS );
	define ( "CACHE_PERIOD", ONE_DAY_IN_SECONDS );
} else {
	define ( "CACHE_PERIOD", $cache_period );
}

date_default_timezone_set ( Settings::get ( "timezone" ) );

if (isset ( $_GET ["output_scripts"] )) {
	getCombinedScripts ();
} else if (isset ( $_GET ["output_stylesheets"] )) {
	getCombinedStylesheets ();
}

$locale = Settings::get ( "locale" );
if ($locale) {
	$locale = splitAndTrim ( $locale );
	array_unshift ( $locale, LC_ALL );
	@call_user_func_array ( "setlocale", $locale );
}

$session_timeout = 60 * intval ( Settings::get ( "session_timeout" ) );

// Session abgelaufen
if (isset ( $_SESSION ["session_begin"] )) {
	if (time () - $_SESSION ["session_begin"] > $session_timeout) {
		session_destroy ();
		header ( "Location: ./" );
		exit ();
	} else {
		$_SESSION ["session_begin"] = time ();
	}
}
function shutdown_function() {
	if (! defined ( "KCFINDER_PAGE" )) {
		add_hook ( "shutdown" );
	}
}

register_shutdown_function ( "shutdown_function" );

$enforce_https = Settings::get ( "enforce_https" );

if (! is_ssl () and $enforce_https !== false) {
	header ( "Location: https://" . $_SERVER ["HTTP_HOST"] . $_SERVER ["REQUEST_URI"] );
	exit ();
}

$moduleManager = new ModuleManager ();
Vars::set ( "disabledModules", $moduleManager->getDisabledModuleNames () );

if (! defined ( "KCFINDER_PAGE" )) {
	ModelRegistry::loadModuleModels ();
	TypeMapper::loadMapping ();
	HelperRegistry::loadModuleHelpers ();
	ControllerRegistry::loadModuleControllers ();
	add_hook ( "before_init" );
	add_hook ( "init" );
	add_hook ( "after_init" );
}

$version = new UliCMSVersion ();
if (! defined ( "UPDATE_CHECK_URL" )) {
	define ( "UPDATE_CHECK_URL", "https://update.ulicms.de/?v=" . urlencode ( implode ( ".", $version->getInternalVersion () ) ) . "&update=" . urlencode ( $version->getUpdate () ) );
}

$pkg = new PackageManager ();
$installed_patches = $pkg->getInstalledPatchNames ();
$installed_patches = implode ( ";", $installed_patches );

if (! defined ( "PATCH_CHECK_URL" )) {
	define ( "PATCH_CHECK_URL", "https://patches.ulicms.de/?v=" . urlencode ( implode ( ".", $version->getInternalVersion () ) ) . "&installed_patches=" . urlencode ( $installed_patches ) );
}

if (! Settings::get ( "session_name" )) {
	Settings::set ( "session_name", uniqid () . "_SESSION" );
}

session_name ( Settings::get ( "session_name" ) );
