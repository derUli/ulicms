<?php 
define("MODULE_ADMIN_HEADLINE", "Kalender");

$required_permission = getconfig("calendar_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);

function fullcalendar_admin(){

if(isset($_GET["calendar_action"]))
   $action = $_GET["calendar_action"];
?>
<?php if(!isset($action)){?>
<a href="<?php echo getModuleAdminSelfPath()?>">Termin eintragen</a>
<?php }?>
<?php
}
 
?>