<?php
include_once "init.php";
function generateSysReport(){
  $str .= "<h1 style='text-align:center;'>";
  $str .= "sysreport für ";
  $str .= "UliCMS ". cms_version();
  $str .= "</h1>";
  $str .= "\n";
  $str .= "PHP Version ".phpversion()."\n";
  $str .= "<h3>Module</h3>";
  $extensions = get_loaded_extensions();
  $str .= "<ol>";
  foreach($extensions as $extension)
    $str .= "<li>".$extension."</li>";
  $str .= "</ol>";
  
  $str .= "<h2>Betriebssystem des Servers</h2>";
  $str .= php_uname()."\n";
  $str .= "<h2>Client</h2>"
  $str .= $_SERVER['HTTP_USER_AGENT']
    
  return $str;
}
header("Content-Type: text/html; charset = UTF-8");

echo nl2br(generateSysReport());