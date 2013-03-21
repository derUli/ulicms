<?php 
include getModulePath("mysql_backup")."mysql_backup_install.php";
mysql_backup_check_install();

function mysql_backup_render(){  
   include_once "mysql_backup_cron.php";
   return "";
}
?>
