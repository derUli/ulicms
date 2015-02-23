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
  $allow_url_fopen = ini_get('allow_url_fopen') ? 'Aktiviert' : 'Deaktiviert';
  $str .= "allow_url_fopen: ".$allow_url_fopen;
  $str .= "<h3>Module</h3>";
  $extensions = get_loaded_extensions();
  $str .= "<ol>";
  foreach($extensions as $extension)
    $str .= "<li>".htmlspecialchars($extension)."</li>";
  $str .= "</ol>";
  
  $str .= "<h2>Betriebssystem des Servers</h2>";
  $str .= php_uname()."\n";
  $str .= "umask: " . umask()."\n";
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
  
  $str .= "<h2>UliCMS</h2>";
  $str .= "Core Version: ". cms_version();
  $str .= "<h3>Installierte Module</h3>";
  $modules = getAllModules();
  
  $str .= "<ol>";
  foreach($modules as $module){
    $str .= "<li>".htmlspecialchars($module)."</li>";
  }
  $str .= "</ol>";
  
  $str .= "<h2>Dateirechte</h2>";
  $files = array("cms-config.php", "modules/", "templates/", ULICMS_ROOT, "content/", "content/cache/", "content/images/", "content/files/", "content/flash/", "content/tmp/", "upload_tmp_dir");
  $str .= "<ol>";
  foreach($files as $file){
        $wrt = is_writable($file)? 'Beschreibbar' : 'Schreibgeschützt';
        $str .= "<li>".htmlspecialchars($file)." ".$wrt."</li>";
        
  }
  
  $str .= "</ol>";
  
    $str .= '<h2>$_SERVER</h2>'."";
  foreach($_SERVER as $key => $value){
       $str .= htmlspecialchars($key) . " = " . htmlspecialchars($value) ."\n";
  }
  
    $str .= '<h2>$_COOKIE</h2>'."";
  foreach($_COOKIE as $key => $value){
       $str .= htmlspecialchars($key) . " = " . htmlspecialchars($value) ."\n";
  }
  
  
  $str .= "<small>generiert durch sysreport 2015-02-23</small>";
  return $str;
}
header("Content-Type: application/octet-stream; charset = UTF-8");
header("Content-Disposition: attachment; filename=sysreport.html;")
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>sysreport</title>
</head>
<body>
<?php echo nl2br(generateSysReport());?>
</body>
</html>