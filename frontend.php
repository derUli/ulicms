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


header("Content-Type: text/html; charset=UTF-8");


if(strtolower(getconfig("disable_cache"))=="on"||strtolower(getconfig("disable_cache"))=="true"||getconfig("disable_cache")=="1"){
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") ." GMT");
  header("Cache-Control: no-cache");
  header("Pragma: no-cache");
  header("Cache-Control: post-check=0, pre-check=0", false);
}


$status = check_status();


if($_GET["rss"]=="rss"){
  require_once "rss.php";
  exit();
}


if(getconfig("redirection")!=""&&getconfig("redirection")!=false){
  header("Location: ".env("redirection"));
  exit();
}


if(strtolower(getconfig("maintenance_mode"))=="on"||strtolower(getconfig("maintenance_mode"))=="true"||getconfig("maintenance_mode")=="1"){
  require_once "templates/maintenance.php";
  die();
}




header("HTTP/1.0 ".$status);



$cached_page_path = buildCacheFilePath($page);
	
if(file_exists($cached_page_path)){
   $cached_content = file_get_contents($cached_page_path);
   if($cached_content){
      die($cached_content);
   }
}
	


require_once "templates/oben.php";
content();
require_once "templates/unten.php";

?>