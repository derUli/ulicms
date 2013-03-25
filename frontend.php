<?php
require_once "init.php";
global $connection;
require_once "templating.php";


// initialize session
session_start();
$_COOKIE[session_name()] = session_id();

if(!empty($_GET["language"])){
   $_SESSION["language"] = basename($_GET["language"]);
}

if(!isset($_SESSION["language"])){
   $_SESSION["language"] = getconfig("default_language");
}


$status = check_status();


if($_GET["rss"]=="rss"){
  require_once "rss.php";
  exit();
}


if(getconfig("redirection")!=""&&getconfig("redirection")!=false){
  header("Location: ".getconfig("redirection"));
  exit();
}




if(strtolower(getconfig("maintenance_mode"))=="on"||strtolower(getconfig("maintenance_mode"))=="true"||getconfig("maintenance_mode")=="1"){
  require_once "templates/maintenance.php";
  die();
}


header("HTTP/1.0 ".$status);
header("Content-Type: text/html; charset=utf-8");

if(file_exists("templates/functions.php")){
   include "templates/functions.php";
}

$cached_page_path = buildCacheFilePath($_SERVER['REQUEST_URI']);


if(file_exists($cached_page_path) and !getconfig("cache_disabled")
   and getenv('REQUEST_METHOD') == "GET"){
   $cached_content = file_get_contents($cached_page_path);
   $last_modified = filemtime($cached_page_path);
    
    if($cached_content and (time() - $last_modified < CACHE_PERIOD)){
      die($cached_content);
      
   }
}

if(!getconfig("cache_disabled" and getenv('REQUEST_METHOD') == "GET") 
   and !file_exists($cached_page_path)){
   ob_start();
}
else if(file_exists($cached_page_path)){
    $last_modified = filemtime($cached_page_path);
    if(time() - $last_modified < CACHE_PERIOD){
      ob_start();
   }
}

$modules = getAllModules();

require_once "templates/oben.php";

for($i=0; $i < count($modules); $i++){
  $before_content_file = getModulePath($modules[$i]).
  $modules[$i]."_before_content.php";
  if(file_exists($before_content_file))
     include $before_content_file;

}

content();

for($i=0; $i < count($modules); $i++){
  $after_content_file = getModulePath($modules[$i]).
  $modules[$i]."_after_content.php";
  if(file_exists($after_content_file))
     include $after_content_file;

}

require_once "templates/unten.php";

$hasModul = containsModule($_GET["seite"]);

if(!getconfig("cache_disabled") and !$hasModul and
   getenv('REQUEST_METHOD') == "GET"){
   $generated_html = ob_get_clean();
   $handle = fopen($cached_page_path, "wb");
   fwrite($handle, $generated_html);
   fclose($handle);
   die($generated_html);
     
}


@include 'cron.php';

?>