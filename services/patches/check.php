<?php
$version = $_REQUEST["v"];
$installed_patches = $_REQUEST["installed_patches"];

if(!$version){
   header("HTTP/1.0 404 Not Found");
   exit();
}

$file = "lists/".basename($version).".txt";

if(!file_exists($file))
   die();

if(!isset($installed_patches) or empty($installed_patches))
   $installed_patches = array();
else
   $installed_patches = explode(";", $installed_patches);

$installed_patches = array_map('trim', $installed_patches);

$content = file_get_contents($file);

$content = explode("\n", $content);

header("Content-type: text/plain; charset=UTF-8");

foreach($content as $line){
  $sline = explode("|", $line);
  $sline = array_map("trim", $sline);
  if(!in_array($sline[0], $installed_patches)){
      echo $line."\n";
  }

}
