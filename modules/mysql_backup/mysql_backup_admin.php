<?php
define("MODULE_ADMIN_HEADLINE", "Automatisches Backup der MySQL-Datenbank");

$required_permission = getconfig("newsletter_required_permission");

if($required_permission === false){
   $required_permission = 40;
}

define("MODULE_ADMIN_REQUIRED_PERMISSION", $required_permission);

define("DATE_FORMAT", getconfig("date_format"));

include getModulePath("newsletter")."newsletter_install.php";
newsletter_check_install();


function mysql_backup_admin(){
?>




<?php }?>
