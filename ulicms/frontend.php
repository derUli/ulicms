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


if(getconfig("redirection") != "" && getconfig("redirection") != false){
     add_hook("before_global_redirection");
     header("Location: " . getconfig("redirection"));
     exit();
     }


@include_once "lib/string_functions.php";

$theme = getconfig("theme");

if(strtolower(getconfig("maintenance_mode")) == "on" || strtolower(getconfig("maintenance_mode")) == "true" || getconfig("maintenance_mode") == "1"){
     add_hook("before_maintenance_message");
     
     header('HTTP/1.0 503 Service Temporarily Unavailable');
     if(file_exists(getTemplateDirPath($theme) . "maintenance.php"))
        require_once getTemplateDirPath($theme) . "maintenance.php";
     else
        throw new Exception("Diese Website ist zurzeit im Wartungsmodus.<br />Bitte später wiederkommen.");
     add_hook("after_maintenance_message");
     die();
     }


header("HTTP/1.0 " . $status);
header("Content-Type: text/html; charset=utf-8");

if(count(getThemeList()) === 0)
     throw new Exception("Keine Themes vorhanden!");


if(!is_dir(getTemplateDirPath($theme)))
     throw new Exception("Das aktivierte Theme existiert nicht!");



if(file_exists(getTemplateDirPath($theme) . "functions.php")){
     include getTemplateDirPath($theme) . "functions.php";
     }

$cached_page_path = buildCacheFilePath($_SERVER['REQUEST_URI']);

$modules = getAllModules();
$hasModul = containsModule(get_requested_pagename());


add_hook("before_html");

$c = getconfig("cache_type");
switch($c){
  case "cache_lite":
     @include "Cache/Lite.php";
     $cache_type = "cache_lite";
     
     break;       
  case "file": default:
     $cache_type = "file";
  break; break;
}

if(file_exists($cached_page_path) and !getconfig("cache_disabled")
         and getenv('REQUEST_METHOD') == "GET" and
         $cache_type === "file"){
         

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
         and !file_exists($cached_page_path) and $cache_type === "file"){
     ob_start();
     }
else if(file_exists($cached_page_path)){
     $last_modified = filemtime($cached_page_path);
     if(time() - $last_modified < CACHE_PERIOD){
         ob_start();
         }
     }
     
  $id = md5($_SERVER['REQUEST_URI']);
     
if(!getconfig("cache_disabled") and !$hasModul and
         getenv('REQUEST_METHOD') == "GET" and $cache_type === "cache_lite"){
  $options = array(
    'lifeTime' => getconfig("cache_period")
);

if(!class_exists("Cache_Lite")){
   throw new Exception("Fehler:<br/>Cache_Lite ist nicht installiert. Bitte stellen Sie den Cache bitte wieder auf Datei-Modus um.");
}
$Cache_Lite = new Cache_Lite($options);

if ($data = $Cache_Lite->get($id)) {
   die($data);
} else {
   ob_start();
}


}

require_once getTemplateDirPath($theme) . "oben.php";


add_hook("before_content");
content();
add_hook("after_content");

require_once getTemplateDirPath($theme) . "unten.php";

add_hook("after_html");

if(!getconfig("cache_disabled") and !$hasModul and
         getenv('REQUEST_METHOD') == "GET" and $cache_type === "cache_lite"){
          $data = ob_get_clean();
          
          if(!defined("EXCEPTION_OCCURRED")){
             $Cache_Lite->save($data, $id);
          }
          echo $data;
          
          add_hook("before_cron");
          @include 'cron.php';
          add_hook("after_cron");
          die();
         
}





if(!getconfig("cache_disabled") and !$hasModul and
         getenv('REQUEST_METHOD') == "GET" and $cache_type === "file"){
     $generated_html = ob_get_clean();
     
     if(!defined("EXCEPTION_OCCURRED")){
       $handle = fopen($cached_page_path, "wb");
       fwrite($handle, $generated_html);
       fclose($handle);
     }
     echo($generated_html);
    
     add_hook("before_cron");
     @include 'cron.php';
     add_hook("after_cron");
     die();
    
     }else{
     add_hook("before_cron");
     @include 'cron.php';
     add_hook("after_cron");
     die();
     }

?>
