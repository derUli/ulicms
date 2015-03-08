<?php
$version = $_REQUEST["version"];
$installed_patches = $_REQUEST["patches"];

if(!$version or !$installed_patches){
   header("HTTP/1.0 404 Not Found");
   exit();
}

$file = "lists/".basename($version).".txt";

if(!file_exists($file))
   die();

$patches = explode(";", $installed_patches);

$$installed_patches = array_map('trim', $$installed_patches);

$content = file_get_contents($file);

$content = explode("\n", $content);

header("Content-type: text/plain; charset=UTF-8");

foreach($content as $line){
  $sline = explode("|", $sline);
  $sline = array_map("trim", $sline);
  if(!in_array($sline[0], $installed_patches)){
      echo $line."\n";
  }

}