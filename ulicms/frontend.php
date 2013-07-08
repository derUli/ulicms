<?php
require_once "init.php";
global $connection;
require_once "templating.php";


// initialize session
session_start();
$_COOKIE[session_name()] = session_id();

add_hook("after_session_start");

if(!empty($_GET["language"])){
   $_SESSION["language"] = basename($_GET["language"]);
}

if(!isset($_SESSION["language"])){
   $_SESSION["language"] = getconfig("default_language");
}


$status = check_status();


if(getconfig("redirection")!=""&&getconfig("redirection")!=false){
  add_hook("before_global_redirection");
  header("Location: ".getconfig("redirection"));
  exit();
}


$theme = getconfig("theme");

if(strtolower(getconfig("maintenance_mode"))=="on"||strtolower(getconfig("maintenance_mode"))=="true"||getconfig("maintenance_mode")=="1"){
  add_hook("before_maintenance_message");
  require_once getTemplateDirPath($theme)."maintenance.php";
    add_hook("after_maintenance_message");
  die();
}


header("HTTP/1.0 ".$status);
header("Content-Type: text/html; charset=utf-8");

if(count(getThemeList()) === 0)
  die("Keine Themes vorhanden!");


if(!is_dir(getTemplateDirPath($theme)))
  die("Das aktivierte Theme existiert nicht!");



if(file_exists(getTemplateDirPath($theme)."functions.php")){
   include getTemplateDirPath($theme)."functions.php";
}

$cached_page_path = buildCacheFilePath($_SERVER['REQUEST_URI']);

$modules = getAllModules();
$hasModul = containsModule(get_requested_pagename());


add_hook("before_html");


if(file_exists($cached_page_path) and !getconfig("cache_disabled")
   and getenv('REQUEST_METHOD') == "GET"){
   $cached_content = file_get_contents($cached_page_path);
   $last_modified = filemtime($cached_page_path);
    
    if($cached_content and (time() - $last_modified < CACHE_PERIOD)){
      echo $cached_content;
      add_hook("before_cron");
      @include 'cron.php';
      add_hook("after_cron");
      die();
      
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


require_once getTemplateDirPath($theme)."oben.php";


add_hook("before_content");
content();
add_hook("after_content");

require_once getTemplateDirPath($theme)."/unten.php";

add_hook("after_html");


if(!getconfig("cache_disabled") and !$hasModul and
   getenv('REQUEST_METHOD') == "GET"){
   $generated_html = ob_get_clean();
   $handle = fopen($cached_page_path, "wb");
   fwrite($handle, $generated_html);
   fclose($handle);
   echo($generated_html);
   
   add_hook("before_cron");
   @include 'cron.php';
   die();
   add_hook("after_cron");
     
} else {
   add_hook("before_cron");
   @include 'cron.php';
   die();
   add_hook("after_cron");
}

?>
