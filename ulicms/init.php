<?php
/**
 * Diese Datei initalisiert das System
 */

// root directory of UliCMS
if (! defined ( "ULICMS_ROOT" )) {
	define ( "ULICMS_ROOT", dirname ( __file__ ) );
}
function uimport($class) {
	$path = str_replace ( "\\", "/", ULICMS_ROOT ) . "/" . $class . ".php";
	return include_once $path;
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
if (! file_existS ( ULICMS_CACHE )) {
	mkdir ( ULICMS_CACHE );
}
include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "logger.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "users_api.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "network.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "settings.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "storages" . DIRECTORY_SEPERATOR . "viewbag.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "storages" . DIRECTORY_SEPERATOR . "vars.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "storages" . DIRECTORY_SEPERATOR . "flags.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "storages" . DIRECTORY_SEPERATOR . "settings_cache.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "modules" . DIRECTORY_SEPERATOR . "module.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "modules" . DIRECTORY_SEPERATOR . "module_manager.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "backend" . DIRECTORY_SEPERATOR . "admin_menu.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "backend" . DIRECTORY_SEPERATOR . "menu_entry.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "settings" . DIRECTORY_SEPERATOR . "base_config.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "settings" . DIRECTORY_SEPERATOR . "settings.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "web" . DIRECTORY_SEPERATOR . "request.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "antispam-features.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "categories.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "pkg" . DIRECTORY_SEPERATOR . "package_manager.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "security" . DIRECTORY_SEPERATOR . "acl.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "pkg" . DIRECTORY_SEPERATOR . "sin_package_installer.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "logger.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "html_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "security_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "number_format_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "antispam_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "http_request_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "string_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "module_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "import_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "export_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "backend_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "exceptions" . DIRECTORY_SEPERATOR . "NotImplementedException.php";

$mobile_detect_as_module = dirname ( __file__ ) . "/content/modules/Mobile_Detect/Mobile_Detect.php";
if (file_exists ( $mobile_detect_as_module )) {
	include_once $mobile_detect_as_module;
}

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "version.php";
function exception_handler($exception) {
	echo $exception->getMessage (), "\n";
	if (! defined ( "EXCEPTION_OCCURRED" )) {
		define ( "EXCEPTION_OCCURRED", true );
	}
}

set_exception_handler ( 'exception_handler' );

// Workaround für Magic Quotes und Register Globals
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "workaround.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "creators" . DIRECTORY_SEPERATOR . "pdf_creator.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "creators" . DIRECTORY_SEPERATOR . "csv_creator.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "creators" . DIRECTORY_SEPERATOR . "json_creator.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "creators" . DIRECTORY_SEPERATOR . "plaintext_creator.php";

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
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "abstract" . DIRECTORY_SEPERATOR . "helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "abstract" . DIRECTORY_SEPERATOR . "model.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "abstract" . DIRECTORY_SEPERATOR . "controller.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "spellchecker.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "registry" . DIRECTORY_SEPERATOR . "helper_registry.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "registry" . DIRECTORY_SEPERATOR . "controller_registry.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "registry" . DIRECTORY_SEPERATOR . "action_registry.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "registry" . DIRECTORY_SEPERATOR . "object_registry.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "type_mapper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "db_functions.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "files.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "mailer.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "file_get_contents_wrapper.php";
require_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "translation.php";
require_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "html5_media.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "database" . DIRECTORY_SEPERATOR . "database.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "template.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "security" . DIRECTORY_SEPERATOR . "encryption.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "files" . DIRECTORY_SEPERATOR . "file.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "files" . DIRECTORY_SEPERATOR . "path.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "users" . DIRECTORY_SEPERATOR . "user.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "users" . DIRECTORY_SEPERATOR . "user_manager.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "web" . DIRECTORY_SEPERATOR . "mailer.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "custom_data.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "localization" . DIRECTORY_SEPERATOR . "translation.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "localization" . DIRECTORY_SEPERATOR . "js_translation.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "files" . DIRECTORY_SEPERATOR . "cache.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "database" . DIRECTORY_SEPERATOR . "db_migrator.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "content.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "link.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "node.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "list_data.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "list.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "module_page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "video_page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "audio_page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "image_page.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "banner.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "banners.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "article.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "comment.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "content_factory.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "groups" . DIRECTORY_SEPERATOR . "group.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "objects" . DIRECTORY_SEPERATOR . "content" . DIRECTORY_SEPERATOR . "custom_fields.php";

Translation::init ();

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

if ($config->db_server == "" or $config->db_user == "") {
	header ( "Location: installer/" );
	exit ();
}

@$connection = Database::connect ( $config->db_server, $config->db_user, $config->db_password );

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

$enforce_https = Settings::get ( "enforce_https" );

if (! is_ssl () and $enforce_https !== false) {
	header ( "Location: https://" . $_SERVER ["HTTP_HOST"] . $_SERVER ["REQUEST_URI"] );
	exit ();
}

$moduleManager = new ModuleManager ();
Vars::set ( "disabledModules", $moduleManager->getDisabledModuleNames () );

if (! defined ( "KCFINDER_PAGE" )) {
	ObjectRegistry::loadModuleObjects ();
	TypeMapper::loadMapping ();
	HelperRegistry::loadModuleHelpers ();
	ControllerRegistry::loadModuleControllers ();
}

add_hook ( "before_init" );
add_hook ( "init" );
add_hook ( "after_init" );

$version = new ulicms_version ();
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
