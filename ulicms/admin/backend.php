<?php
require_once "../init.php";

session_start();
setcookie(session_name(), session_id());
add_hook("after_session_start");

require_once "../version.php";
require_once "inc/logincheck.php";
require_once "inc/queries.php";
@include_once "inc/sort_direction.php";


define("_SECURITY", true);




if(isset($_SESSION["ulicms_login"]))
    {
     $eingeloggt = $_SESSION["ulicms_login"];
     db_query("UPDATE " . tbname("admins") . " SET last_action = " . time() . " WHERE id = " . $_SESSION["login_id"]);
     }else{
     $eingeloggt = false;
     }

header("Content-Type: text/html; charset=UTF-8");


if(isset($_REQUEST["ajax_cmd"])){
     include_once "inc/ajax_handler.php";
     exit();
     }



require_once "inc/header.php";
if(!$eingeloggt){
     if(isset($_GET["register"])){
         require_once "inc/registerform.php";
         }
    else{
         require_once "inc/loginform.php";
         }
     }else{
     require_once "inc/adminmenu.php";
    
    
    
     if($_GET["action"] == "" || $_GET["action"] == "home"){
         require_once "inc/dashboard.php";
         }
    else if($_GET["action"] == "contents"){
         require_once "inc/contents.php";
         }
    
    else if($_GET["action"] == "pages"){
         require_once "inc/pages.php";
         }   
    else if($_GET["action"] == "news"){
         require_once "inc/news.php";
         }
    else if($_GET["action"] == "comments"){
         require_once "inc/comments.php";
         }
    else if($_GET["action"] == "categories"){
       require_once "inc/categories.php";
         }
    else if($_GET["action"] == "pages_edit"){
         require_once "inc/edit_page.php";
         }
    else if($_GET["action"] == "pages_new"){
         require_once "inc/add_page.php";
         }
    else if($_GET["action"] == "banner"){
         require_once "inc/banner.php";
         }
    else if($_GET["action"] == "banner_new"){
         require_once "inc/banner_new.php";
         }
    else if($_GET["action"] == "banner_edit"){
         require_once "inc/banner_edit.php";
         }
    else if($_GET["action"] == "admins"){
         require_once "inc/admins.php";
         }
    else if($_GET["action"] == "groups"){
         require_once "inc/groups.php";
         }
    else if($_GET["action"] == "admin_new"){
         require_once "inc/admins_new.php";
         }
    else if($_GET["action"] == "admin_edit"){
         require_once "inc/admins_edit.php";
         }
    else if($_GET["action"] == "settings_categories"){
         require_once "inc/settings_categories.php";
         }
    else if($_GET["action"] == "settings"){
         require_once "inc/settings.php";
         }
    else if($_GET["action"] == "settings_simple"){
         require_once "inc/settings_simple.php";
         }
    else if($_GET["action"] == "spam_filter"){
         require_once "inc/spamfilter_settings.php";
         }
    else if($_GET["action"] == "customize_menu"){
         require_once "inc/customize_menu.php";
         }
    else if($_GET["action"] == "key_new"){
         require_once "inc/key_new.php";
         }
    else if($_GET["action"] == "key_edit"){
         require_once "inc/key_edit.php";
         }
    
    else if($_GET["action"] == "templates"){
         require_once "inc/templates.php";
         }else if($_GET["action"] == "media"){
         require_once "inc/media.php";
         }
    else if($_GET["action"] == "images" || $_GET["action"] == "files" || $_GET["action"] == "flash"){
         require_once "inc/filemanager.php";
         }
    else if($_GET["action"] == "modules"){
         require_once "inc/modules.php";
         }
    else if($_GET["action"] == "available_modules"){
         require_once "inc/available_modules.php";
         }
    else if($_GET["action"] == "install_modules"){
         require_once "inc/install_modules.php";
         }
    
    else if($_GET["action"] == "info"){
         require_once "inc/info.php";
         }
    
    else if($_GET["action"] == "info"){
         require_once "inc/info.php";
         }
    
    else if($_GET["action"] == "system_update"){
         require_once "inc/system_update.php";
         }
    else if($_GET["action"] == "motd"){
         require_once "inc/motd.php";
         }
    
    else if($_GET["action"] == "edit_profile"){
         require_once "inc/edit_profile.php";
         }
    
    else if($_GET["action"] == "logo_upload"){
         require_once "inc/logo.php";
         }
    else if($_GET["action"] == "languages"){
         require_once "inc/languages.php";
         }
    
    else if($_GET["action"] == "cache"){
         require_once "inc/cache_settings.php";
         }
    else if($_GET["action"] == "module_settings"){
         require_once "inc/module_settings.php";
         }
    else if($_GET["action"] == "other_settings"){
         require_once "inc/other_settings.php";
         }
    
    else if($_GET["action"] == "pkg_settings"){
         require_once "inc/pkg_settings.php";
         }else if($_GET["action"] == "design"){
         require_once "inc/design.php";
         }
     }

require_once "inc/footer.php";
@include '../cron.php';
db_close($connection);
exit();
?>
