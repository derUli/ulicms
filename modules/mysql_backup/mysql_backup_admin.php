<?php
define("MODULE_ADMIN_HEADLINE", "Automatisches Backup der MySQL-Datenbank");

$required_permission = getconfig("mysql_backup_required_permission");

if($required_permission === false){
   $required_permission = 50;
}

define("MODULE_ADMIN_REQUIRED_PERMISSION", $required_permission);


include getModulePath("mysql_backup")."mysql_backup_install.php";
mysql_backup_check_install();


function mysql_backup_admin(){
?>




<?php }?>
