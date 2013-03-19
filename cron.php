<?php 
// Cronjobs

$modules = getAllModules();
for($i=0; $i < count($modules); $i++){
  $currentModule = $modules[$i];
  $cronjob_file = getModulePath($currentModule).$currentModule."_cron.php";
  if(file_exists($cronjob_file))
     @include $cronjob_file;
}


?>