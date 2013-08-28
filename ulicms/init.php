<?php
/**
 * Diese Datei initalisiert das System
 */

// Workaround für Magic Quotes und Register Globals
include "lib/workaround.php";

// if config exists require_config else redirect to installer
if(file_exists("cms-config.php")){
     require_once "cms-config.php";
     }
else if(file_exists("../cms-config.php")){
     require_once "../cms-config.php";
     }
else if(file_exists("backend.php") and is_dir("../installer")){
     header("Location: ../installer/");
     exit();
     }
else if(is_dir("installer")){
     header("Location: installer/");
     exit();
     }else{
     die("Can't include cms-config.php. Starting installer failed, too.");
     }


include_once "lib/db_functions.php";
require_once "api.php";

// define Constants
define('CR', "\r"); // carriage return; Mac
define('LF', "\n"); // line feed; Unix
define('CRLF', "\r\n"); // carriage return and line feed; Windows
define('BR', '<br />' . LF); // HTML Break
define("ONE_DAY_IN_SECONDS", 60 * 60 * 24);


function noperms(){
     echo "<p>Sie haben nicht die Berechtigung, um auf diese Seite zugreifen zu dürfen.<br/>Bitte loggen Sie sich als <u>admin</u> ein oder fragen Sie den Administrator der Webseite.</p>";
     return false;
     }

function startsWith($haystack, $needle, $case = true){
     if($case){
         return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
         }
     return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
     }

function endsWith($haystack, $needle, $case = true){
     if($case){
         return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
         }
     return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
     }


global $connection, $config;
$config = new config();

if($config -> mysql_server == "" or $config -> mysql_user == ""){
     header("Location: installer/");
     exit();
     }

@$connection = mysql_connect($config -> mysql_server, $config -> mysql_user, $config -> mysql_password);
if($connection === false){
     die("Fehler: Die Verbindung zum MySQL Server konnte nicht hergestellt werden.");
     }

mysql_query("SET NAMES 'utf8'");

 if(file_exists("installer/installer.php") or file_exists("../installer/installer.php")){
     header("Content-Type: text/html; charset=utf-8");
     die("<p>Bitte löschen Sie den Ordner \"installer\" vom Server.<br/>
     Das CMS kann erst betrieben werden, nach dem der Installer gelöscht wurde.
     Dies ist ein Sicherheitsmerkmal von UliCMS.</p>");
     exit();
     }





define("MYSQL_CONNECTION", $connection);
$select = mysql_select_db($config -> mysql_database);
if(!$select){
     die("Fehler: Die Datenbank " . $config -> mysql_database . " existiert nicht.\n");
     }


$error_reporting = getconfig("error_reporting");

if($error_reporting !== false)
     error_reporting($error_reporting);


$memory_limit = getconfig("memory_limit");

if($memory_limit !== false)
     @ini_set('memory_limit', $memory_limit);

if(getconfig("zlib.output_compression"))
     @ini_set("zlib.output_compression", 1);
else
     @ini_set("zlib.output_compression", 0);


@ob_implicit_flush(1);


$cache_period = getconfig("cache_period");

// Prüfen ob Cache Gültigkeitsdauer gesetzt ist.
// Ansonsten auf Standardwert setzen
if($cache_period === false){
     setconfig("cache_period", ONE_DAY_IN_SECONDS);
     define("CACHE_PERIOD", ONE_DAY_IN_SECONDS);
     }else{
     define("CACHE_PERIOD", $cache_period);
     }

// Proxy für file_get_contents
// Für den Fall, dass der Server sich hinter einem Proxy befindet
// z.B. in größeren Unternehmen
$proxy = getconfig("proxy");
if($proxy and function_exists("stream_context_set_default")){
     $context_array = array('http' => array('proxy' => $proxy,
             'request_fulluri' => true));
     stream_context_set_default($context_array);
     }






// check four allowed_html config var
// if not exists create with default value
if(!getconfig("allowed_html")){
     setconfig("allowed_html",
         "<i><u><b><strong><em><ul><li><ol><a><span>");
     }


// Falls nicht gesetzt, robots auf Standardwert setzen
if(!getconfig("robots")){
     setconfig("robots", "index,follow");
     }

// Prüfen ob Zeitzone gesetzt ist
$timezone = getconfig("timezone");

// Wenn nicht, Zeitzone auf Standardwert setzen
if(!$timezone){
     setconfig("timezone", "Europe/Berlin");
     }
 date_default_timezone_set(getconfig("timezone"));

// Set locale
if(getconfig("locale") === false){
     setconfig("locale", 'de_DE');
     }

$locale = getconfig("locale");
@setlocale (LC_ALL, $locale);

if(!getconfig("session_timeout")){
     setconfig("session_timeout", 60);
     }




$session_timeout = 60 * getconfig("session_timeout");


// Session abgelaufen
if(isset($_SESSION["session_begin"])){
     if(time() - $_SESSION["session_begin"] > $session_timeout)
        {
         session_destroy();
         header("Location: ./");
         exit();
         }
    else{
         $_SESSION["session_begin"] = time();
        
         }
     }


$enforce_https = getconfig("enforce_https");

if($_SERVER["HTTPS"] != "on" and $enforce_https !== false)
{
     header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
     exit();
     }


add_hook("before_init");
add_hook("init");
add_hook("after_init");

include_once "version.php";

$version = new ulicms_version();

define("UPDATE_CHECK_URL", "http://www.ulicms.de/updatecheck.php?v=" .
     urlencode(
        implode(".", $version -> getInternalVersion())));
