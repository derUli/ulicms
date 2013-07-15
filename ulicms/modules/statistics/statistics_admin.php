<?php 
define("MODULE_ADMIN_HEADLINE", "Besucherstatistiken");

$required_permission = getconfig("statistics_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);

function fullcalendar_admin(){


}
 
?>