<?php 

$empty_trash_days = getconfig("empty_trash_days");

if($empty_trash_days === false)
   $empty_trash_days = 30;


// Papierkorb für Seiten Cronjob
$empty_trash_timestamp = $empty_trash_days * (60 * 60 * 24);
mysql_query("DELETE FROM ".tbname("content")." WHERE ".time()." -  `deleted_at` > $empty_trash_timestamp")or die(mysql_error());


// Cronjobs der Module
$modules = getAllModules();
for($i=0; $i < count($modules); $i++){
  $currentModule = $modules[$i];
  $cronjob_file = getModulePath($currentModule).$currentModule."_cron.php";
  if(file_exists($cronjob_file))
     @include $cronjob_file;
}


?>