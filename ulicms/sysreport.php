<?php
include_once "init.php";

@session_start();
function generateSysReport(){
  $str .= "<h1 style='text-align:center;'>";
  $str .= "sysreport für ";
  $str .= "UliCMS ". cms_version();
  $str .= "</h1>";
  $str .= "\n";
  $str .= "PHP Version ".phpversion()."\n";
  $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();

  $str .= "upload_tmp_dir: " . $tmp_dir."\n";
  $allow_url_fopen = ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled';
  $str .= "allow_url_fopen: ".$allow_url_fopen;
  $str .= "<h3>Module</h3>";
  $extensions = get_loaded_extensions();
  $str .= "<ol>";
  foreach($extensions as $extension)
    $str .= "<li>".htmlspecialchars($extension)."</li>";
  $str .= "</ol>";
  
  $str .= "<h2>Betriebssystem des Servers</h2>";
  $str .= php_uname()."\n";
  $str .= "<h2>Client</h2>";
  $str .= "Useragent: " . htmlspecialchars($_SERVER['HTTP_USER_AGENT'])."\n";
  
  $str.= "<h2>MySQL</h2>";
  $query = db_query("SELECT @@VERSION as version");
  $result = db_fetch_object($query);
  $str .= "Server Version: ". htmlspecialchars($result->version)."\n";
  
  $str .= "<h2>Derzeit angemeldete Benutzer</h2>";
  $users_online = db_query("SELECT * FROM " . tbname("users") . " WHERE last_action > " . (time() - 300) . " ORDER BY username");
  $str .= "<ol>";
  while($row = db_fetch_object($users_online)){
     $str .= "<li>".htmlspecialchars($row -> username)."</li>";
  }
    
  $str .= "</ol>";
  return $str;
}
header("Content-Type: text/html; charset = UTF-8");

echo nl2br(generateSysReport());