<?php
/**
 * Diese Datei initalisiert das System
 */

// root directory of UliCMS
if (! defined ( "ULICMS_ROOT" )) {
	define ( "ULICMS_ROOT", dirname ( __file__ ) );
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

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "base_config.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "antispam-features.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "categories.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "package_manager.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "acl.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "logger.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "html_helper.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "security_helper.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "number_format_helper.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "antispam_helper.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "http_request_helper.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "string_helper.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "NotImplementedException.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "import_helper.php";

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "helper" . DIRECTORY_SEPERATOR . "export_helper.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "Mobile_Detect.php";

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

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "pdf_creator.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "csv_creator.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "json_creator.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "classes" . DIRECTORY_SEPERATOR . "plaintext_creator.php";

// if config exists require_config else redirect to installer
$path_to_config = dirname ( __file__ ) . DIRECTORY_SEPERATOR . "cms-config.php";

if (file_exists ( $path_to_config )) {
	require_once $path_to_config;
} 

else if (is_dir ( "installer" )) {
	header ( "Location: installer/" );
	exit ();
} else {
	throw new Exception ( "Can't include cms-config.php. Starting installer failed, too." );
}

global $connection, $config;
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

include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "db_functions.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "mailer.php";
include_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "file_get_contents_wrapper.php";
require_once dirname ( __file__ ) . DIRECTORY_SEPERATOR . "api.php";
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
	echo "<p class=\"ulicms_error\">" . TRANSLATION_NO_PERMISSIONS . "</p>";
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

global $connection, $config;
$config = new config ();

if ($config->db_server == "" or $config->db_user == "") {
	header ( "Location: installer/" );
	exit ();
}

@$connection = db_connect ( $config->db_server, $config->db_user, $config->db_password );

if ($connection === false) {
	throw new Exception ( "Fehler: Die Verbindung zum Datenbank Server konnte nicht hergestellt werden." );
}

$path_to_installer = dirname ( __file__ ) . DIRECTORY_SEPERATOR . "installer" . DIRECTORY_SEPERATOR . "installer.php";

if (file_exists ( $path_to_installer )) {
	header ( "Content-Type: text/html; charset=utf-8" );
	throw new Exception ( "<p>Bitte löschen Sie den Ordner \"installer\" vom Server.<br/>
     Das CMS kann erst betrieben werden, nach dem der Installer gelöscht wurde.
     Dies ist ein Sicherheitsmerkmal von UliCMS.</p>" );
	exit ();
}

$select = schema_select ( $config->db_database );

if (! $select) {
	throw new Exception ( "Fehler: Die Datenbank " . $config->db_database . " existiert nicht.\n" );
}

$existing_tables = array ();
if (! defined ( "SKIP_TABLE_CHECK" )) {
	$existing_tables = db_get_tables ();
	$required_tables = array (
			tbname ( "users" ),
			tbname ( "banner" ),
			tbname ( "categories" ),
			tbname ( "content" ),
			tbname ( "groups" ),
			tbname ( "installed_patches" ),
			tbname ( "videos" ),
			tbname ( "audio" ),
			tbname ( "languages" ),
			tbname ( "log" ),
			tbname ( "mails" ),
			tbname ( "history" ),
			tbname ( "settings" ) 
	);
	
	for($i = 0; $i < count ( $required_tables ); $i ++) {
		$table = $required_tables [$i];
		if (! in_array ( $table, $existing_tables )) {
			if (! headers_sent ())
				header ( "Content-Type: text/html; charset=UTF-8" );
			
			throw new Exception ( "Fehler: Die vom System benötigte Tabelle '$table' ist nicht in der Datenbank vorhanden.<br/>Bitte prüfen Sie die Installation!" );
			exit ();
		}
	}
}

$useragent = getconfig ( "useragent" );
if ($useragent) {
	define ( "ULICMS_USERAGENT", $useragent );
} else {
	define ( "ULICMS_USERAGENT", "UliCMS Release " . cms_version () );
}

@ini_set ( 'user_agent', ULICMS_USERAGENT );

if (! getconfig ( "hide_meta_generator" ))
	@header ( 'X-Powered-By: UliCMS Release ' . cms_version () );

$memory_limit = getconfig ( "memory_limit" );

if ($memory_limit !== false)
	@ini_set ( 'memory_limit', $memory_limit );

if (in_array ( tbname ( "log" ), $existing_tables )) {
	$log_ip = getconfig ( "log_ip" );
	log_request ( $log_ip );
}

$cache_period = getconfig ( "cache_period" );

// Prüfen ob Cache Gültigkeitsdauer gesetzt ist.
// Ansonsten auf Standardwert setzen
if ($cache_period === false) {
	setconfig ( "cache_period", ONE_DAY_IN_SECONDS );
	define ( "CACHE_PERIOD", ONE_DAY_IN_SECONDS );
} else {
	define ( "CACHE_PERIOD", $cache_period );
}

// check four allowed_html config var
// if not exists create with default value
if (! getconfig ( "allowed_html" )) {
	setconfig ( "allowed_html", "<i><u><b><strong><em><ul><li><ol><a><span>" );
}

// Falls nicht gesetzt, robots auf Standardwert setzen
if (! getconfig ( "robots" )) {
	setconfig ( "robots", "index,follow" );
}

// Prüfen ob Zeitzone gesetzt ist
$timezone = getconfig ( "timezone" );

// Wenn nicht, Zeitzone auf Standardwert setzen
if (! $timezone) {
	setconfig ( "timezone", "Europe/Berlin" );
}
date_default_timezone_set ( getconfig ( "timezone" ) );

if (isset ( $_GET ["output_scripts"] )) {
	getCombinedScripts ();
} else if (isset ( $_GET ["output_stylesheets"] )) {
	getCombinedStylesheets ();
}

$locale = getconfig ( "locale" );
if ($locale) {
	$locale = splitAndTrim ( $locale );
	array_unshift ( $locale, LC_ALL );
	@call_user_func_array ( "setlocale", $locale );
}

if (! getconfig ( "session_timeout" )) {
	setconfig ( "session_timeout", 60 );
}

$session_timeout = 60 * getconfig ( "session_timeout" );

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

$enforce_https = getconfig ( "enforce_https" );

if ($_SERVER ["HTTPS"] != "on" and $enforce_https !== false) {
	header ( "Location: https://" . $_SERVER ["HTTP_HOST"] . $_SERVER ["REQUEST_URI"] );
	exit ();
}

if(!getconfig("disable_hsts") and is_ssl()){
   $maxage = getconfig("hsts_maxage");
   if($maxage === false)
      $maxage = 10 * 30;
   
   $maxage = intval($maxage);

   $includeSubDomains = getconfig("hsts_include_subdomains");
   if(!$includeSubDomains)
      $includeSubDomains =  "";
    $str = "Strict-Transport-Security: max-age=".$maxage;
    if(!empty($includeSubDomains))
       $str .= "; ".$includeSubDomains;

    $str = trim($str);

    header($str);
}

add_hook ( "before_init" );
add_hook ( "init" );
add_hook ( "after_init" );

$version = new ulicms_version ();

define ( "UPDATE_CHECK_URL", "http://update.ulicms.de/?v=" . urlencode ( implode ( ".", $version->getInternalVersion () ) ) . "&update=" . urlencode ( $version->getUpdate () ) );

if (in_array ( tbname ( "installed_patches" ), $existing_tables )) {
	$pkg = new PackageManager ();
	$installed_patches = $pkg->getInstalledPatchNames ();
	$installed_patches = implode ( ";", $installed_patches );
} else {
	$installed_patches = "";
}

define ( "PATCH_CHECK_URL", "http://patches.ulicms.de/?v=" . urlencode ( implode ( ".", $version->getInternalVersion () ) ) . "&installed_patches=" . urlencode ( $installed_patches ) );

if (! getconfig ( "session_name" )) {
	setconfig ( "session_name", uniqid () . "_SESSION" );
}

session_name ( getconfig ( "session_name" ) );

@include_once "lib/string_functions.php";
