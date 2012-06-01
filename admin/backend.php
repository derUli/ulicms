<?php 
session_start();
setcookie(session_name(),session_id());

require_once "../init.php";
require_once "../version.php";
require_once "inc/logincheck.php";
require_once "inc/queries.php";


define("_SECURITY", true);


header("Content-Type: text/html; charset=UTF-8");

if(isset($_SESSION["ulicms_login"]))
{
  $eingeloggt=$_SESSION["ulicms_login"];
}else{
  $eingeloggt=false;
}

require_once "inc/header.php";
if(!$eingeloggt){
	require_once "inc/loginform.php";
}else{
	require_once "inc/adminmenu.php";



if($_GET["action"]==""||$_GET["action"]=="home"){
	require_once "inc/dashboard.php";
}
else if($_GET["action"]=="news"){
	require_once "inc/news.php";
}
else if($_GET["action"]=="add_news"){
	require_once "inc/add_news.php";
}
else if($_GET["action"]=="edit_news"){
	require_once "inc/edit_news.php";
}
else if($_GET["action"]=="contents"){
	require_once "inc/contents.php";
}

else if($_GET["action"]=="pages"){
	require_once "inc/pages.php";
}
else if($_GET["action"]=="pages_edit"){
	require_once "inc/edit_page.php";
}
else if($_GET["action"]=="pages_new"){
	require_once "inc/add_page.php";
}
else if($_GET["action"]=="banner"){
	require_once "inc/banner.php";
}
else if($_GET["action"]=="banner_new"){
	require_once "inc/banner_new.php";
}
else if($_GET["action"]=="banner_edit"){
	require_once "inc/banner_edit.php";
}
else if($_GET["action"]=="admins"){
	require_once "inc/admins.php";
}
else if($_GET["action"]=="admin_new"){
	require_once "inc/admins_new.php";
}
else if($_GET["action"]=="admin_edit"){
	require_once "inc/admins_edit.php";
}
else if($_GET["action"]=="settings"){
	require_once "inc/settings.php";
}
else if($_GET["action"]=="settings_simple"){
	require_once "inc/settings_simple.php";
}
else if($_GET["action"]=="key_new"){
	require_once "inc/key_new.php";
}
else if($_GET["action"]=="key_edit"){
	require_once "inc/key_edit.php";
}

else if($_GET["action"]=="templates"){
	require_once "inc/templates.php";
}else if($_GET["action"] == "media"){
	require_once "inc/media.php";
}
else if($_GET["action"]=="images" ||$_GET["action"]=="files" ||$_GET["action"]=="flash"){
	require_once "inc/filemanager.php";
}

else if($_GET["action"]=="info"){
	require_once "inc/info.php";
}

}

require_once "inc/footer.php";

mysql_close($connection);
exit();
?>
