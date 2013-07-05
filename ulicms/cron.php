<?php 

$empty_trash_days = getconfig("empty_trash_days");

if($empty_trash_days === false)
   $empty_trash_days = 30;

// Papierkorb für Seiten Cronjob
$empty_trash_timestamp = $empty_trash_days * (60 * 60 * 24);
db_query("DELETE FROM ".tbname("content")." WHERE ".time()." -  `deleted_at` > $empty_trash_timestamp")or die(mysql_error());


// Cronjobs der Module
$modules = getAllModules();

for($i=0; $i < count($modules); $i++){
  $currentModule = $modules[$i];
  $cronjob_file = getModulePath($currentModule).$currentModule."_cron.php";
  if(file_exists($cronjob_file))
     @include $cronjob_file;
}



?>
<?php
error_reporting(E_ALL);
$version = new ulicms_version();

$developmentVersion = "";

if($version->getDevelopmentVersion())
  $developmentVersion = " Entwickler-Version";

// Start Call Home //
$cfg_script = "UliCMS ".$version->getVersion().
" (v".
join(".", $version->getInternalVersion()).$developmentVersion.")";
$cfg_url    = "http://www.ulicms.de/chs/api.php";


$urlfrom     = $_SERVER['HTTP_HOST'];
$folderfrom  = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME'])."/");
$var_url     = $urlfrom.$folderfrom;

$chs0     = $cfg_script."#".$var_url;
$chs      = base64_encode($chs0);

if(!function_exists('file_get_contents_wrapper')){
   include_once "lib/file_get_contents_wrapper.php";
}
@file_get_contents_wrapper("$cfg_url?chs=$chs");
exit();


?>