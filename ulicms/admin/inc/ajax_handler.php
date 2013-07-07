<?php 
$ajax_cmd = $_REQUEST["ajax_cmd"];

switch($ajax_cmd){
case "users_online":
   include "inc/users_online.php";
break;case "users_online_dashboard":
   include "inc/users_online_dashboard.php";
break;
default:
   echo "Unknown Call";
break;
}
?>