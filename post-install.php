<?php 
if(!function_exists("setconfig"))
   include "init.php";
// Post-Install Script für blog2facebook Script
if(!in_array(getAllModules(), "blog")){
   echo "<p style='color:red;'>Bitte installieren Sie erst das Blogmodul und dann erneut das blog2facebook Modul</p>";
} else {
if(!getconfig("facebook_app_id"))
   setconfig("facebook_app_id", "xxxxxxxxxxxxxxxxx");

if(!getconfig("facebook_app_secret"))
   setconfig("facebook_app_secret", "xxxxxxxxxxxxxxxxxxxxxxx");
}

?>