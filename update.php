<?php 
// Require config and init-script
require_once "cms-config.php";
require_once "init.php";



$cache_dir = "content/cache";
if(!is_dir($cache_dir)){
   mkdir($cache_dir, 0777);
}

//@unlink("update.php");

header("Location: admin/");
exit();

?>