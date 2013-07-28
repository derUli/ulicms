<?php

// get a config variable
function getconfig($key){
     $connection = MYSQL_CONNECTION;
     $ikey = mysql_real_escape_string($key);
     $query = db_query("SELECT * FROM " . tbname("settings") . " WHERE name='$key'");
     if(mysql_num_rows($query) > 0){
         while($row = mysql_fetch_object($query)){
             return $row -> value;
             }
         }
    else{
         return false;
         }
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
         $query = mysql_query("SELECT * FROM " . tbname("content") . " WHERE systemname='" . get_requested_pagename() . "'");
        
         if(mysql_num_rows($query) > 0){
             $fetch = mysql_fetch_object($query);
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
     $info = @file_get_contents_Wrapper(UPDATE_CHECK_URL);
    
     if(!$info or trim($info) === "")
         return false;
     else
         return $info;
    
     }

function getThemeList(){
     return getThemesList();
     }

function getThemesList(){
     $themes = Array();
     if(is_admin_dir())
         $templateDir = "../templates/";
     else
         $templateDir = "templates/";
    
     $folders = scanDir($templateDir);
     natcasesort($folders);
     for($i = 0; $i < count($folders); $i++){
         $f = $templateDir . ($folders[$i]) . "/";
         if(is_dir($f)){
             if(is_file($f . "oben.php") and is_file($f . "unten.php")
                     and is_file($f . "style.css"))
                 array_push($themes, $folders[$i]);
            
             }
         }
    
     natcasesort($themes);
    
     return $themes;
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
    
     $unique_identifier = $request_uri . $language;
    
     return "content/cache/" . md5($unique_identifier) . ".html";
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

function buildSEOUrl($page = false){
     if($page === false)
         $page = get_requested_pagename();
    
    
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


function getAllModules(){
     // Frontend Directory
    if(is_file("cms-config.php")){
         $module_folder = "modules/";
         }
     // Backend Directory
    else{
         $module_folder = "../modules/";
         }
    
    
     $available_modules = Array();
     $directory_content = scandir($module_folder);
    
     natcasesort($directory_content);
     for($i = 0;$i < count($directory_content);$i++){
         $module_init_file = $module_folder . $directory_content[$i] . "/" .
         $directory_content[$i] . "_main.php";
        
        
         if($directory_content[$i] != ".." and $directory_content[$i] != "."){
             if(is_file($module_init_file)){
                 array_push($available_modules, $directory_content[$i]);
                 }
             }
         }
     natcasesort($available_modules);
     return $available_modules;
    
     }


// replace Shortcodes with modules
function replaceShortcodesWithModules($string){
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
        
         }
     return $string;
     }


// get page id by systemname
function getPageIDBySystemname($systemname){
     $query = db_query("SELECT systemname, id FROM `" . tbname("content") . "` where systemname='" . mysql_real_escape_string($systemname) . "'");
     if(mysql_num_rows($query) > 0){
         $row = mysql_fetch_object($query);
         return $row -> id;
         }else{
         return null;
         }
     }


// get PageSystemnameByID
function getPageSystemnameByID($id){
     $query = db_query("SELECT systemname, id FROM `" . tbname("content") . "` where id=" . intval($id));
     if(mysql_num_rows($query) > 0){
         $row = mysql_fetch_object($query);
         return $row -> systemname;
         }else{
         return "-";
         }
     }

// Get systemnames of all pages
function getAllSystemNames(){
     $query = db_query("SELECT systemname,id FROM `" . tbname("content") . "` WHERE `deleted_at`IS NULL ORDER BY systemname");
     $returnvalues = Array();
     while($row = mysql_fetch_object($query)){
         array_push($returnvalues, $row -> systemname);
         }
    
     return $returnvalues;
    
     }


// Sprachcodes abfragen und als Array zurück geben
function getAllLanguages(){
     $query = db_query("SELECT * FROM `" . tbname("languages") . "` ORDER BY language_code");
     $returnvalues = Array();
     while($row = mysql_fetch_object($query)){
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
     $key = mysql_real_escape_string($key);
     db_query("DELETE FROM " . tbname("settings") .
         " WHERE name='$key'");
     return mysql_affected_rows() > 0;
     }

// Set a configuration Variable;
function setconfig($key, $value){
     $query = db_query("SELECT * FROM " . tbname("settings") . " WHERE name='$key'");
    
     if(mysql_num_rows($query) > 0){
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




// Check if site contains a module
function containsModule($page, $module = false){
     $query = db_query("SELECT * FROM " . tbname("content") . " WHERE systemname = '" .
         mysql_real_escape_string($page) . "'");
     $dataset = mysql_fetch_assoc($query);
     $content = $dataset["content"];
     $content = str_replace("&quot;", "\"", $content);
    
     if($module)
         return preg_match("/\[module=\"" . preg_quote($module) . "\"\]/",
             $content);
     else
         return preg_match("/\[module=\".+\"\]/",
             $content);
    
     }

// API-Aufruf zur Deinstallation eines Moduls
// Ruft uninstall Script auf, falls vorhanden
// Löscht anschließend den Ordner modules/$name
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
     return isset($_SESSION["group"]);
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
     return $config -> mysql_prefix . $name;
     }

// returns version number of UliCMS Core
function cms_version(){
     require_once "version.php";
     $v = new ulicms_version();
     return $v -> getVersion();
     }


function is_admin(){
     return has_permissions(50);
     }

require_once "users_api.php";
require_once "legacy.php";
