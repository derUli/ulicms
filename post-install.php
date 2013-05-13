<?php 
if(!function_exists("setconfig"))
   include "init.php";
// Post-Install Script für blog2facebook Script
if(!in_array("blog", getAllModules())){
   echo "<p style='color:red;'>Bitte installieren Sie erst das Blogmodul und dann erneut das blog2facebook Modul</p>";
} else {
if(!getconfig("facebook_app_id"))
   setconfig("facebook_app_id", "xxxxxxxxxxxxxxxxx");

if(!getconfig("facebook_app_secret"))
   setconfig("facebook_app_secret", "xxxxxxxxxxxxxxxxxxxxxxx");
}

if(!getconfig("facebook_user_page_id"))
   setconfig("facebook_user_page_id", xxxxxxxxxxxxxxxxxxxxxxx);


if(!getconfig("base_blog_page"))
setconfig("base_blog_page", "http://".$_SERVER["SERVER_NAME"]."/blog.html");

mysql_query("ALTER TABLE ".tbname("blog")." ADD COLUMN posted_to_facebook BOOL DEFAULT 0")or print("<p>".mysql_error()."</p>");
?>