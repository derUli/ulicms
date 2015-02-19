<?php

function getLanguageFilePath($lang = "de", $component = null){
     // Todo Module Language Files
    return ULICMS_ROOT . "/lang/" . $lang . ".php";
     }

// returns site protocl
// http:// or https://
function site_protocol() {
    if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')  return $protocol = 'https://'; else return $protocol = 'http://';
}

function strbool($value)
{
     return $value ? 'true' : 'false';
     }

function getFontSizes(){
     return array("xx-small", "x-small", "smaller", "small", "medium", "large", "larger", "x-large", "xx-large");
     }

function getModuleName($module){
     $name_file = getModulePath($module) .
     $module . "_name.php";
     if(!file_exists($name_file)){
         return $module;
         }
     include_once $name_file;
     $name_function = $module . "_name";
     if(function_exists($name_function)){
         return call_user_func($name_function);
         }else{
         return $module;
         }
     }

function getLanguageNameByCode($code){
     $query = db_query("SELECT name FROM `" . tbname("languages") . "` WHERE language_code = '" . db_escape($code) . "'");
     $retval = $code;
     if(db_num_rows($query) > 0){
         $result = db_fetch_object($query);
         $retval = $result -> name;
         }
    
     return $retval;
     }

function getAvailableBackendLanguages(){
     $langdir = ULICMS_ROOT . "/lang/";
     $list = scandir($langdir);
     sort($list);
     $retval = array();
     for($i = 0; $i < count($list); $i++){
         if(endsWith($list[$i], ".php")){
             array_push($retval, basename($list[$i], ".php"));
             }
         }
    
     return $retval;
     }

function getSystemLanguage(){
     if(isset($_SESSION["system_language"])){
         $lang = $_SESSION["system_language"];
         }else if(getconfig("system_language")){
         $lang = getconfig("system_language");
         }else{
         $lang = "de";
         }
    
     if(!file_exists(getLanguageFilePath($lang))){
         $lang = "de";
         }
    
     return $lang;
     }


function getStatusCodeByNumber($nr){
     $http_codes = array(
        100 => 'Continue',
         101 => 'Switching Protocols',
         102 => 'Processing',
         200 => 'OK',
         201 => 'Created',
         202 => 'Accepted',
         203 => 'Non-Authoritative Information',
         204 => 'No Content',
         205 => 'Reset Content',
         206 => 'Partial Content',
         207 => 'Multi-Status',
         300 => 'Multiple Choices',
         301 => 'Moved Permanently',
         302 => 'Found',
         303 => 'See Other',
         304 => 'Not Modified',
         305 => 'Use Proxy',
         306 => 'Switch Proxy',
         307 => 'Temporary Redirect',
         400 => 'Bad Request',
         401 => 'Unauthorized',
         402 => 'Payment Required',
         403 => 'Forbidden',
         404 => 'Not Found',
         405 => 'Method Not Allowed',
         406 => 'Not Acceptable',
         407 => 'Proxy Authentication Required',
         408 => 'Request Timeout',
         409 => 'Conflict',
         410 => 'Gone',
         411 => 'Length Required',
         412 => 'Precondition Failed',
         413 => 'Request Entity Too Large',
         414 => 'Request-URI Too Long',
         415 => 'Unsupported Media Type',
         416 => 'Requested Range Not Satisfiable',
         417 => 'Expectation Failed',
         418 => 'I\'m a teapot',
         422 => 'Unprocessable Entity',
         423 => 'Locked',
         424 => 'Failed Dependency',
         425 => 'Unordered Collection',
         426 => 'Upgrade Required',
         449 => 'Retry With',
         450 => 'Blocked by Windows Parental Controls',
         500 => 'Internal Server Error',
         501 => 'Not Implemented',
         502 => 'Bad Gateway',
         503 => 'Service Unavailable',
         504 => 'Gateway Timeout',
         505 => 'HTTP Version Not Supported',
         506 => 'Variant Also Negotiates',
         507 => 'Insufficient Storage',
         509 => 'Bandwidth Limit Exceeded',
         510 => 'Not Extended'
        );
    
     return $nr . " " . $http_codes[$nr];
     }


function ulicms_redirect($url = "http://www.ulicms.de", $status = 302){
     header("HTTP/1.0 " . getStatusCodeByNumber($status));
     header("Location: " . $url);
     exit();
    
     }

function getDomainByLanguage($language){
     $domainMapping = getconfig("domain_to_language");
    
     if(!empty($domainMapping)){
         $domainMapping = explode("\n", $domainMapping);
         for($i = 0; $i < count($domainMapping); $i++){
             $line = trim($domainMapping[$i]);
             if(!empty($line)){
                 $line = explode("=>", $line);
                
                 if(count($line) > 1){
                     $line[0] = trim($line[0]);
                     $line[1] = trim($line[1]);
                     if(!empty($line[0]) and !empty($line[1])){
                        
                         if($line[1] == $language){
                             return $line[0];
                            
                             }
                        
                        
                         }
                     }
                 }
             }
        
        
         }
     return null;
     }


// encodeURIComponent() is needed when working with accents
// If not used, generate a JS error in CKEDITOR link plugin
function encodeURIComponent($str){
    
     $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
     return strtr(rawurlencode($str), $revert);
    
     }


function setLanguageByDomain(){
     $domainMapping = getconfig("domain_to_language");
    
     if(!empty($domainMapping)){
         $domainMapping = explode("\n", $domainMapping);
         for($i = 0; $i < count($domainMapping); $i++){
             $line = trim($domainMapping[$i]);
             if(!empty($line)){
                 $line = explode("=>", $line);
                 if(count($line) > 1){
                     $line[0] = trim($line[0]);
                     $line[1] = trim($line[1]);
                    
                     if(!empty($line[0]) and !empty($line[1])){
                         $domain = $_SERVER["HTTP_HOST"];
                        
                         if($line[0] == $domain and in_array($line[1], getAllLanguages())){
                             $_SESSION["language"] = $line[1];
                            
                             return true;
                            
                             }
                        
                        
                         }
                     }
                 }
             }
        
        
         }
     return false;
     }


function getCacheType(){
     $c = getconfig("cache_type");
     switch($c){
     case "cache_lite":
         @include "Cache/Lite.php";
         $cache_type = "cache_lite";
        
         break;
     case "file": default:
         $cache_type = "file";
         break;
         break;
         }
    
     return $cache_type;
     }

function getOnlineUsers(){
     $users_online = db_query("SELECT * FROM " . tbname("users") . " WHERE last_action > " . (time() - 300) . " ORDER BY username");
     $users = array();
     while($row = db_fetch_object($users_online)){
         array_push($users, $row -> username);
         }
     return $users;
     }

// get a config variable
function getconfig($key){
     $ikey = db_escape($key);
     $query = db_query("SELECT * FROM " . tbname("settings") . " WHERE name='$key'");
     if(db_num_rows($query) > 0){
         while($row = db_fetch_object($query)){
             return $row -> value;
             }
         }
    else{
         return false;
         }
     }


 function rootDirectory(){
     $pageURL = 'http';
     if ($_SERVER["HTTPS"] == "on"){
         $pageURL .= "s";
         }
     $pageURL .= "://";
     $dirname = dirname($_SERVER["REQUEST_URI"]);
     $dirname = str_replace("\\", "/", $dirname);
     $dirname = trim($dirname, "/");
     if($dirname != ""){
         $dirname = "/" . $dirname . "/";
         }else{
         $dirname = "/";
         }
     if ($_SERVER["SERVER_PORT"] != "80"){
         $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $dirname;
         }else{
         $pageURL .= $_SERVER["SERVER_NAME"] . $dirname;
         }
     return $pageURL;
     }


if(!function_exists("get_host")){
     function get_host(){
         if ($host = $_SERVER['HTTP_X_FORWARDED_HOST'])
        {
             $elements = explode(',', $host);
            
             $host = trim(end($elements));
             }
        else
            {
             if (!$host = $_SERVER['HTTP_HOST'])
            {
                 if (!$host = $_SERVER['SERVER_NAME'])
                {
                     $host = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
                     }
                 }
             }
        
         // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);
        
         return trim($host);
         }
    
     }

function get_mime($file){
     if (function_exists("finfo_file")){
         $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
         $mime = finfo_file($finfo, $file);
         finfo_close($finfo);
         return $mime;
         }else if (function_exists("mime_content_type")){
         return mime_content_type($file);
         }else if (!stristr(ini_get("disable_functions"), "shell_exec")){
         // http://stackoverflow.com/a/134930/1593459
        $file = escapeshellarg($file);
         $mime = shell_exec("file -bi " . $file);
         return $mime;
         }else{
         return false;
         }
     }


function clearAPCCache(){
     if(!function_exists("apc_clear_cache")){
         return false;
         }
     apc_clear_cache();
     apc_clear_cache('user');
     apc_clear_cache('opcode');
     return true;
    
     }
 function clearCache(){
     add_hook("before_clear_cache");
     $cache_type = getconfig("cache_type");
     // Es gibt zwei verschiedene Cache Modi
    // Cache_Lite und File
    // Cache_Lite leeren
    if($cache_type === "cache_lite" and class_exists("Cache_Lite")){
         $Cache_Lite = new Cache_Lite($options);
         $Cache_Lite -> clean();
         }else{
         // File leeren
        if(is_admin_dir())
             SureRemoveDir("../content/cache", false);
         else
             SureRemoveDir("content/cache", false);
         }
    
    
     if(function_exists("apc_clear_cache")){
         clearAPCCache();
         }
    
    
     add_hook("after_clear_cache");
     }


// sind wir gerade im Adminordner?
function is_admin_dir(){
     return basename(getcwd()) === "admin";
     }

function add_hook($name){
     $modules = getAllModules();
     for($hook_i = 0; $hook_i < count($modules); $hook_i++){
         $file = getModulePath($modules[$hook_i]) .
         $modules[$hook_i] . "_" . $name . ".php";
         if(file_exists($file)){
             @include $file;
             }
         }
    
     }

function register_action($name, $file){
    
     global $actions;
     $modules = getAllModules();
     $actions[$name] = $file;
     return $actions;
     }

function remove_action($name){
     global $actions;
     $retval = false;
     if(isset($action[$name])){
         unset($name);
         $retval = true;
         }
     return $retval;
     }

// Check for Secure HTTP Connection (SSL)
function is_ssl(){
     return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
         || $_SERVER['SERVER_PORT'] == 443);
     }


// Returns the language code of the current language
// If $current is true returns language of the current page
// else it returns $_SESSION["language"];
function getCurrentLanguage($current = true){
     if($current){
         $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname='" . get_requested_pagename() . "'");
        
         if(db_num_rows($query) > 0){
             $fetch = db_fetch_object($query);
             return $fetch -> language;
             }
         }
    
    
     if(isset($_SESSION["language"]))
         return basename($_SESSION["language"]);
     else
         return basename(getconfig("default_language"));
     }

// Auf automatische aktualisieren prüfen.
// Rückgabewert: ein String oder False
function checkForUpdates(){
     include_once "../lib/file_get_contents_wrapper.php";
     $info = @file_get_contents_Wrapper(UPDATE_CHECK_URL, true);
    
     if(!$info or trim($info) === "")
         return false;
     else
         return $info;
    
     }

function getThemeList(){
     return getThemesList();
     }

function getThemesList(){
     $pkg = new packageManager();
     return $pkg -> getInstalledPackages('themes');
     }

 // Make title url safe
if(!function_exists("cleanString")){
     function cleanString($string, $separator = '-'){
         $accents = array('Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f', 'Ä' => 'Ae', 'ä' => 'ae',
             'Ö' => 'Oe', 'ö' => 'oe', 'Ü' => 'Ue', 'ü' => 'ue', 'ß' => 'ss'
            );
         $string = strtr($string, $accents);
         $string = strtolower($string);
         $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
         $string = preg_replace('{ +}', ' ', $string);
         $string = trim($string);
         $string = str_replace(' ', $separator, $string);
        
         return $string;
         }
    
     }


function getTemplateDirPath($sub = "default"){
     if(is_admin_dir())
         $templateDir = "../templates/";
     else
         $templateDir = "templates/";
    
     $templateDir = $templateDir . $sub . "/";
    
     return $templateDir;
     }

function getModuleAdminSelfPath(){
     $self_path = $_SERVER["REQUEST_URI"];
     $self_path = str_replace('"', '', $self_path);
     $self_path = str_replace("'", '', $self_path);
    
     return $self_path;
     }


function replace_num_entity($ord)
{
     $ord = $ord[1];
     if (preg_match('/^x([0-9a-f]+)$/i', $ord, $match))
        {
         $ord = hexdec($match[1]);
         }
    else
        {
         $ord = intval($ord);
         }
    
     $no_bytes = 0;
     $byte = array();
    
     if ($ord < 128)
    {
         return chr($ord);
         }
    elseif ($ord < 2048)
    {
         $no_bytes = 2;
         }
    elseif ($ord < 65536)
    {
         $no_bytes = 3;
         }
    elseif ($ord < 1114112)
    {
         $no_bytes = 4;
         }
    else
        {
         return;
         }
    
     switch($no_bytes)
    {
     case 2:
        {
             $prefix = array(31, 192);
             break;
             }
         case 3:
        {
             $prefix = array(15, 224);
             break;
             }
         case 4:
        {
             $prefix = array(7, 240);
             }
         }
    
     for ($i = 0; $i < $no_bytes; $i++)
    {
     $byte[$no_bytes - $i - 1] = (($ord & (63 * pow(2, 6 * $i))) / pow(2, 6 * $i)) & 63 | 128;
     }

 $byte[0] = ($byte[0] & $prefix[0]) | $prefix[1];

 $ret = '';
 for ($i = 0; $i < $no_bytes; $i++)
{
     $ret .= chr($byte[$i]);
     }

 return $ret;
 }

// This Returns the current full URL
// for example: http://www.homepage.de/news.html?single=title
function getCurrentURL(){
 $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
 $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
 $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
 $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
 return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
 }


function buildCacheFilePath($request_uri){
 $language = $_SESSION["language"];
 if(!$language){
     $language = getconfig("default_language");
     }


 $unique_identifier = $request_uri . $language . strbool(is_mobile());

 return "content/cache/" . md5($unique_identifier) . ".tmp";
 }



function get_translation($name){
 $name = strtoupper($name);
 foreach (get_defined_constants() as $key => $value){
     if(startsWith($key, "TRANSLATION_") and $key == "TRANSLATION_" . $name){
         return $value;
         }
     }
 return null;
}

function translation($name){
 echo get_translation($name);
}

function translate($name){
 translation($name);
}

function SureRemoveDir($dir, $DeleteMe){
 if(!$dh = @opendir($dir)) return;
 while (false !== ($obj = readdir($dh))){
     if($obj == '.' || $obj == '..') continue;
     if (!@unlink($dir . '/' . $obj)) SureRemoveDir($dir . '/' . $obj, true);
     }

 closedir($dh);
 if ($DeleteMe){
     @rmdir($dir);
     }
 }


/**
 * Generate path to Page
 * Argumente
 * String $page (Systemname)
 * Rückgabewert String im Format
 * ../seite.html
 * bzw.
 * seite.html;
 */

function buildSEOUrl($page = false, $redirection = null){
 if($page === false)
     $page = get_requested_pagename();


 if(startsWith($redirection, "#")){
     return $redirection;
     }

 if($page === get_frontpage())
     return "./";



 $seo_url = "";

 if(is_file("backend.php"))
     $seo_url .= "../";




 $seo_url .= $page;
 $seo_url .= ".html";

 return $seo_url;

 }

function getModulePath($module){
 // Frontend Directory
if(is_file("cms-config.php")){
     $module_folder = "modules/";
     }
 // Backend Directory
else{
     $module_folder = "../modules/";
     }
 $available_modules = Array();
 return $module_folder . $module . "/";
 }

function getModuleAdminFilePath($module){
 return getModulePath($module) .
 $module . "_admin.php";
 }

function getModuleMainFilePath($module){
 return getModulePath($module) .
 $module . "_main.php";

 }

function getModuleUninstallScriptPath($module){
 return getModulePath($module) .
 $module . "_uninstall.php";

 }


function find_all_files($dir)
{
 $root = scandir($dir);
 $result = array();
 foreach($root as $value)
{
     if($value === '.' || $value === '..'){
         continue;
         }
     if(is_file("$dir/$value")){
         $result[] = "$dir/$value";
         continue;
         }
     foreach(find_all_files("$dir/$value") as $value)
    {
         $result[] = $value;
         }
     }
 return $result;
 }

/**
 * outputCSV creates a line of CSV and outputs it to browser
 */
function outputCSV($array){
 $fp = fopen('php://output', 'w'); // this file actual writes to php output
 fputcsv($fp, $array);
 fclose($fp);
}

/**
 * getCSV creates a line of CSV and returns it.
 */
function getCSV($array){
 ob_start(); // buffer the output ...
 outputCSV($array);
 return ob_get_clean(); // ... then return it as a string!
}


/**
 * Output buffer flusher
 * Forces a flush of the output buffer to screen useful for displaying long loading lists eg: bulk emailers on screen 
 * Stops the end user seeing loads of just plain old white and thinking the browser has crashed on long loading pages.
 */
function fcflush()
{
 static $output_handler = null;
 if ($output_handler === null){
     $output_handler = @ini_get('output_handler');
     }
 if ($output_handler == 'ob_gzhandler'){
     // forcing a flush with this is very bad
    return;
     }
 flush();
 if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false){
     ob_flush();
     }else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE){
     @ob_end_flush();
     @ob_start();
     }
 }

function convertLineEndingsToLF($s){
 // Normalize line endings using Global
// Convert all line-endings to UNIX format
$s = str_replace(CRLF, LF, $s);
 $s = str_replace(CR, LF, $s);
 // Don't allow out-of-control blank lines
$s = preg_replace("/\n{2,}/", LF . LF, $s);
 return $s;
 }


function isModuleInstalled($name){
 return in_array($name, getAllModules());
 }

function getAllModules(){
 $pkg = new packageManager();
 return $pkg -> getInstalledPackages('modules');
 }


function no_cache(){
 if(!defined("NO_CACHE"))
     define("NO_CACHE", true);
}

// replace Shortcodes with modules
function replaceShortcodesWithModules($string, $replaceOther = true){
 if($replaceOther){
     $string = str_replace('[title]', get_title(), $string);
    
     ob_start();
     logo();
     $string = str_replace('[logo]', ob_get_clean(), $string);
    
     ob_start();
     motto();
     $string = str_replace('[motto]', ob_get_clean(), $string);
    
     ob_start();
     motto();
     $string = str_replace('[slogan]', ob_get_clean(), $string);
    
     $current_page = get_page();
     $string = str_replace('[category]', get_category(), $string);
     }

 $allModules = getAllModules();
 for($i = 0;$i <= count($allModules);$i++){
     $thisModule = $allModules[$i];
     $stringToReplace1 = '[module="' . $thisModule . '"]';
     $stringToReplace2 = '[module=&quot;' . $thisModule . '&quot;]';
    
     $module_mainfile_path = getModuleMainFilePath($thisModule);
    
     if(is_file($module_mainfile_path) and (strstr($string, $stringToReplace1) or strstr($string, $stringToReplace2))){
         require_once $module_mainfile_path;
         if(function_exists($thisModule . "_render")){
             $html_output = call_user_func($thisModule . "_render");
             }
        else{
             $html_output = "<p class='ulicms_error'>Das Modul " . $thisModule .
             " konnte nicht geladen werden.</p>";
             }
        
         }
    else{
         $html_output = "<p class='ulicms_error'>Das Modul " . $thisModule .
         " konnte nicht geladen werden.</p>";
         }
    
     $string = str_replace($stringToReplace1, $html_output, $string);
     $string = str_replace($stringToReplace2, $html_output, $string);
    
     $string = str_replace('[title]', get_title(), $string);
    
     }
 return $string;
 }


// get page id by systemname
function getPageIDBySystemname($systemname){
 $query = db_query("SELECT systemname, id FROM `" . tbname("content") . "` where systemname='" . db_escape($systemname) . "'");
 if(db_num_rows($query) > 0){
     $row = db_fetch_object($query);
     return $row -> id;
     }else{
     return null;
     }
 }


function getPageSystemnameByID($id){
 $query = db_query("SELECT systemname, id FROM `" . tbname("content") . "` where id=" . intval($id));
 if(db_num_rows($query) > 0){
     $row = db_fetch_object($query);
     return $row -> systemname;
     }else{
     return "-";
     }
 }

function getPageTitleByID($id){
 $query = db_query("SELECT title, id FROM `" . tbname("content") . "` where id=" . intval($id));
 if(db_num_rows($query) > 0){
     $row = db_fetch_object($query);
     return $row -> title;
     }else{
     return "[" . TRANSLATION_NONE . "]";
     }
 }



// Get systemnames of all pages
function getAllPagesWithTitle(){
 $query = db_query("SELECT systemname, id, title FROM `" . tbname("content") . "` WHERE `deleted_at` IS NULL ORDER BY systemname");
 $returnvalues = Array();
 while($row = db_fetch_object($query)){
     $a = Array($row -> title, $row -> systemname . ".html");
     array_push($returnvalues, $a);
     if(containsModule($row -> systemname, "blog")){
        
         $sql = "select title, seo_shortname from " . tbname("blog") . " ORDER by datum DESC";
         $query_blog = db_query($sql);
         while($row_blog = db_fetch_object($query_blog)){
             $title = $row -> title . " -> " . $row_blog -> title;
             $url = $row -> systemname . ".html" . "?single=" . $row_blog -> seo_shortname;
             $b = Array($title, $url);
             array_push($returnvalues, $b);
             }
        
        
         }
     }



 return $returnvalues;

 }

// Get all pages
function getAllPages($lang = null, $order = "systemname", $exclude_hash_links = true){
if(!$lang){
     $query = db_query("SELECT * FROM `" . tbname("content") .
         "` WHERE `deleted_at` IS NULL ORDER BY $order");
     }else{
    
     $query = db_query("SELECT * FROM `" . tbname("content") .
         "` WHERE `deleted_at` IS NULL AND language ='" . db_escape($lang) . "' ORDER BY $order");
     }
 $returnvalues = Array();
 while($row = db_fetch_assoc($query)){
     if(!($exclude_hash_links and startsWith($row["redirection"], "#"))){
         array_push($returnvalues, $row);
         }
    
     }

 return $returnvalues;

 }

// Get systemnames of all pages
function getAllSystemNames($lang = null){
if(!$lang){
     $query = db_query("SELECT systemname,id FROM `" . tbname("content") .
         "` WHERE `deleted_at` IS NULL AND redirection NOT LIKE '#%' ORDER BY systemname");
     }else{
    
     $query = db_query("SELECT systemname,id FROM `" . tbname("content") .
         "` WHERE `deleted_at` IS NULL  AND redirection NOT LIKE '#%' AND language ='" . db_escape($lang) . "' ORDER BY systemname");
     }
 $returnvalues = Array();
 while($row = db_fetch_object($query)){
     array_push($returnvalues, $row -> systemname);
     }

 return $returnvalues;

 }


// Sprachcodes abfragen und als Array zurück geben
function getAllLanguages(){
 $query = db_query("SELECT * FROM `" . tbname("languages") . "` ORDER BY language_code");
 $returnvalues = Array();
 while($row = db_fetch_object($query)){
     array_push($returnvalues, $row -> language_code);
     }
 return $returnvalues;


 }




// get URL to UliCMS
function the_url(){
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on"){
     $pageURL .= "s";
     }
 $pageURL .= "://";
 $dirname = dirname($_SERVER["REQUEST_URI"]);
 $dirname = str_replace("\\", "/", $dirname);
 $dirname = str_replace("admin", "", $dirname);
 $dirname = trim($dirname, "/");
 if($dirname != ""){
     $dirname = "/" . $dirname . "/";
     }else{
     $dirname = "/";
     }
 if ($_SERVER["SERVER_PORT"] != "80"){
     $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $dirname;
     }else{
     $pageURL .= $_SERVER["SERVER_NAME"] . $dirname;
     }
 return $pageURL;
 }


function file_extension($filename){
 $ext = explode(".", $filename);
 $ext = end($ext);
 return $ext;
 }

// Remove an configuration variable
function deleteconfig($key){
 $key = db_escape($key);
 db_query("DELETE FROM " . tbname("settings") .
     " WHERE name='$key'");
 return db_affected_rows() > 0;
 }

// Set a configuration Variable;
function setconfig($key, $value){
 $query = db_query("SELECT * FROM " . tbname("settings") . " WHERE name='$key'");

 if(db_num_rows($query) > 0){
     db_query("UPDATE " . tbname("settings") . " SET value='$value' WHERE name='$key'");
     }else{
    
     db_query("INSERT INTO " . tbname("settings") . " (name, value) VALUES('$key', '$value')");
     }

 }


function is__writable($path)
{

 if ($path{strlen($path)-1} == '/')
    
     return is__writable($path . uniqid(mt_rand()) . '.tmp');

 elseif (file_exists($path) && preg_match('/\.tmp/', $path))
    {
    
     if (!($f = @fopen($path, 'w+')))
         return false;
     fclose($f);
     unlink($path);
     return true;
    
     }
else
    
     return 0; // Or return error - invalid path...




























 }



// Gibt die Identifier aller Menüs zurück.
// Zusätzliche Navigationsmenüs können definiert werden,
// durch setzen von additional_menus
function getAllMenus(){
 $menus = Array("left", "top", "right", "bottom", "none");
 $additional_menus = getconfig("additional_menus");

 if($additional_menus){
     $additional_menus = explode(";", $additional_menus);
     foreach($additional_menus as $m){
         array_push($menus, $m);
         }
     }
 return $menus;
 }

// Check if site contains a module
function containsModule($page = null, $module = false){
if(is_null($page))
     $page = get_requested_pagename();

 $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname = '" .
     db_escape($page) . "'");
 $dataset = db_fetch_assoc($query);
 $content = $dataset["content"];
 $content = str_replace("&quot;", "\"", $content);

 if($module)
     return preg_match("/\[module=\"" . preg_quote($module) . "\"\]/",
         $content);
 else
     return preg_match("/\[module=\".+\"\]/",
         $content);

 }

 function page_has_html_file($page){
 $query = db_query("SELECT `html_file` FROM " . tbname("content") . " WHERE systemname = '" .
     db_escape($page) . "'");
 $dataset = db_fetch_assoc($query);


 $html_file = $dataset["html_file"];

 if(empty($html_file) or is_null($html_file))
     return null;

 $html_file = dirname(__file__) . "/content/files/" . $html_file;

 if(!endsWith($html_file, ".html") && !endsWith($html_file, ".htm")){
     $html_file = $html_file . ".html";
     }

 return $html_file;


 }

// API-Aufruf zur Deinstallation eines Moduls
// Ruft uninstall Script auf, falls vorhanden
// Löscht anschließend den Ordner modules/$name
//  @TODO dies in die PackageManager Klasse verschieben
function uninstall_module($name, $type = "module"){
 // Nur Admins können Module löschen
if(!is_admin())
     return false;

 $name = trim($name);
 $name = basename($name);
 $name = trim($name);

 // Verhindern, dass der Modulordner oder gar das ganze
// CMS gelöscht werden kann
if($name == "." or $name == ".." or empty($name))
     return false;

 if($type === "module"){
     $moduleDir = getModulePath($name);
     // Modul-Ordner entfernen
    if(is_dir($moduleDir)){
         $uninstall_script = getModuleUninstallScriptPath($name);
         // Uninstall Script ausführen, sofern vorhanden
        if(is_file($uninstall_script))
             include $uninstall_script;
        
         sureRemoveDir($moduleDir, true);
         return !is_dir($moduleDir);
         }
     }else if($type === "theme"){
     $cTheme = getconfig("theme");
     $allThemes = getThemeList();
     if(in_array($name, $allThemes) and $cTheme !== $name){
         $theme_path = getTemplateDirPath($name);
         sureRemoveDir($theme_path, true);
         return !is_dir($theme_path);
         }
    
     }
}


// Ist der User eingeloggt
function is_logged_in(){
 return isset($_SESSION["logged_in"]);
 }

// Hat der Nutzer die notwendige Berechtigung
function has_permissions($mod){
 if(!isset($_SESSION["group"]))
     return false;

 return $_SESSION["group"] >= $mod;
 }

// Alias für is_logged_in
function logged_in(){
 return is_logged_in();
 }



// Tabellenname zusammensetzen
function tbname($name){
 require_once "cms-config.php";
 $config = new config();
 return $config -> db_prefix . $name;
 }

// returns version number of UliCMS Core
function cms_version(){
 require_once "version.php";
 $v = new ulicms_version();
 return implode(".", $v -> getInternalVersion());
 }


function is_mobile(){

 // Get the user agent
$user_agent = $_SERVER['HTTP_USER_AGENT'];

 // Create an array of known mobile user agents
// This list is from the 21 October 2010 WURFL File.
// Most mobile devices send a pretty standard string that can be covered by
// one of these.  I believe I have found all the agents (as of the date above)
// that do not and have included them below.  If you use this function, you
// should periodically check your list against the WURFL file, available at:
// http://wurfl.sourceforge.net/
$mobile_agents = Array(
    
    
    "240x320",
     "acer",
     "acoon",
     "acs-",
     "abacho",
     "ahong",
     "airness",
     "alcatel",
     "amoi",
     "android",
     "anywhereyougo.com",
     "applewebkit/525",
     "applewebkit/532",
     "asus",
     "audio",
     "au-mic",
     "avantogo",
     "becker",
     "benq",
     "bilbo",
     "bird",
     "blackberry",
     "blazer",
     "bleu",
     "cdm-",
     "compal",
     "coolpad",
     "danger",
     "dbtel",
     "dopod",
     "elaine",
     "eric",
     "etouch",
     "fly " ,
     "fly_",
     "fly-",
     "go.web",
     "goodaccess",
     "gradiente",
     "grundig",
     "haier",
     "hedy",
     "hitachi",
     "htc",
     "huawei",
     "hutchison",
     "inno",
     "ipad",
     "ipaq",
     "ipod",
     "jbrowser",
     "kddi",
     "kgt",
     "kwc",
     "lenovo",
     "lg ",
     "lg2",
     "lg3",
     "lg4",
     "lg5",
     "lg7",
     "lg8",
     "lg9",
     "lg-",
     "lge-",
     "lge9",
     "longcos",
     "maemo",
     "mercator",
     "meridian",
     "micromax",
     "midp",
     "mini",
     "mitsu",
     "mmm",
     "mmp",
     "mobi",
     "mot-",
     "moto",
     "nec-",
     "netfront",
     "newgen",
     "nexian",
     "nf-browser",
     "nintendo",
     "nitro",
     "nokia",
     "nook",
     "novarra",
     "obigo",
     "palm",
     "panasonic",
     "pantech",
     "philips",
     "phone",
     "pg-",
     "playstation",
     "pocket",
     "pt-",
     "qc-",
     "qtek",
     "rover",
     "sagem",
     "sama",
     "samu",
     "sanyo",
     "samsung",
     "sch-",
     "scooter",
     "sec-",
     "sendo",
     "sgh-",
     "sharp",
     "siemens",
     "sie-",
     "softbank",
     "sony",
     "spice",
     "sprint",
     "spv",
     "symbian",
     "tablet",
     "talkabout",
     "tcl-",
     "teleca",
     "telit",
     "tianyu",
     "tim-",
     "toshiba",
     "tsm",
     "up.browser",
     "utec",
     "utstar",
     "verykool",
     "virgin",
     "vk-",
     "voda",
     "voxtel",
     "vx",
     "wap",
     "wellco",
     "wig browser",
     "wii",
     "windows ce",
     "wireless",
     "xda",
     "xde",
     "zte"
    );

 // Pre-set $is_mobile to false.
$is_mobile = false;

 // Cycle through the list in $mobile_agents to see if any of them
// appear in $user_agent.
foreach ($mobile_agents as $device){
    
     // Check each element in $mobile_agents to see if it appears in
    // $user_agent.  If it does, set $is_mobile to true.
    if (stristr($user_agent, $device)){
        
         $is_mobile = true;
        
         // break out of the foreach, we don't need to test
        // any more once we get a true value.
        break;
         }
     }

 return $is_mobile;
}

 function func_enabled($func){
 $disabled = explode(',', ini_get('disable_functions'));
 foreach ($disabled as $disableFunction){
     $is_disabled[] = trim($disableFunction);
     }
 if (in_array($func, $is_disabled)){
     $it_is_disabled["m"] = $func . '() has been disabled for security reasons in php.ini';
     $it_is_disabled["s"] = 0;
     }else{
     $it_is_disabled["m"] = $func . '() is allow to use';
     $it_is_disabled["s"] = 1;
     }
 return $it_is_disabled;
 }


function is_admin(){
 $acl = new ACL();
 $permissions = $acl -> getDefaultACLAsJSON(true, true);
 foreach($permissions as $permission => $value){
     if(!$acl -> hasPermission($permission)){
         return false;
         }
     }
 return true;
}

require_once "users_api.php";
require_once "legacy.php";
