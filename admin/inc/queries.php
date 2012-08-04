<?php 
if($_GET["action"]=="save_settings"&&isset($_POST["save_settings"])){
  setconfig("homepage_title", mysql_real_escape_string($_POST["homepage_title"]));
  setconfig("homepage_owner", mysql_real_escape_string($_POST["homepage_owner"]));
  setconfig("motto", mysql_real_escape_string($_POST["homepage_motto"]));
  setconfig("meta_keywords", mysql_real_escape_string($_POST["meta_keywords"]));
  setconfig("meta_description", mysql_real_escape_string($_POST["meta_description"]));
  setconfig("language", mysql_real_escape_string($_POST["language"]));
  setconfig("visitors_can_register", intval(isset($_POST["visitors_can_register"])));
  setconfig("maintenance_mode", intval(isset($_POST["maintenance_mode"])));
  setconfig("email", mysql_real_escape_string($_POST["email"]));
  setconfig("max_news", (int)$_POST["max_news"]);
  setconfig("frontpage", mysql_real_escape_string($_POST["frontpage"]));
  setconfig("comment_mode", mysql_real_escape_string($_POST["comment_mode"]));
  setconfig("disqus_id", mysql_real_escape_string($_POST["disqus_id"]));
  setconfig("facebook_id", mysql_real_escape_string($_POST["facebook_id"]));
  setconfig("items_in_rss_feed", intval($_POST["items_in_rss_feed"]));
  header("Location: index.php?action=settings_simple");
  exit();
}

if($_GET["action"]=="pages_delete" && $_SESSION["group"]>=40){
  $page=mysql_real_escape_string($_GET["page"]);
  $query=mysql_query("DELETE FROM ".tbname("content")." WHERE id='$page'",$connection);
  header("Location: index.php?action=pages");
exit();
}


if(!empty($_POST["save_template"])&&!empty($_POST["code"])&&$_SESSION["group"]>=40){
$save="../templates/".basename($_POST["save_template"]);
if(is_file($save)&&is_writable($save)){
$handle=fopen($save,"w");
fwrite($handle,$_POST["code"]);
fclose($handle);
header("Location: index.php?action=templates&save=true");
exit();
}else{

header("Location: index.php?action=templates&save=false");
exit();
}

}


if($_GET["action"]=="key_delete" && $_SESSION["group"]>=40){
$key=intval($_GET["key"]);
$query=mysql_query("DELETE FROM ".tbname("settings")." WHERE id='$key'",$connection);
header("Location: index.php?action=settings");
exit();
}

if($_GET["action"]=="banner_delete" && $_SESSION["group"]>=40){
$banner=intval($_GET["banner"]);
$query=mysql_query("DELETE FROM ".tbname("banner")." WHERE id='$banner'",$connection);
header("Location: index.php?action=banner");
exit();
}


if($_GET["action"]=="admin_delete" && $_SESSION["group"]>=40){
$admin=intval($_GET["admin"]);
$query=mysql_query("DELETE FROM ".tbname("admins")." WHERE id='$admin'",$connection);
header("Location: index.php?action=admins");
exit();
}


if($_POST["add_page"]=="add_page"){
	if($_POST["system_title"]!=""){
	
	$system_title = mysql_real_escape_string($_POST["system_title"]);
	$page_title = mysql_real_escape_string($_POST["page_title"]);
	$activated = intval($_POST["activated"]);
	$page_content = mysql_real_escape_string($_POST["page_content"]);
	$comments_enabled = (int)$_POST["comments_enabled"];
	$notinfeed = (int)$_POST["notinfeed"];
	$redirection = mysql_real_escape_string($_POST["redirection"]);
	$menu = mysql_real_escape_string($_POST["menu"]);
	$position = (int)$_POST["position"];
	$parent = mysql_real_escape_string($_POST["parent"]);
		
	mysql_query("INSERT INTO ".tbname("content").
	" (systemname,title,content,parent, active,created,lastmodified,autor,comments_enabled,notinfeed,redirection,menu,position) VALUES('$system_title','$page_title','$page_content','$parent', $activated,".time().", ".time().",".$_SESSION["login_id"].", ".$comments_enabled .",$notinfeed, '$redirection', '$menu', $position)",$connection)or die(mysql_error());


header("Location: index.php?action=pages");
exit();

}

}



if($_POST["add_news"]=="add_news" && $_SESSION["group"]>=20){
$title=mysql_real_escape_string($_POST["title"]);
$activated=intval($_POST["activated"]);
$content=mysql_real_escape_string($_POST["news_content"]);
$date=time();
$autor=$_SESSION["login_id"];
$query=mysql_query("INSERT INTO ".tbname("news")." 
(title,content,active, autor,date) VALUES('$title','$content',$activated,$autor,$date)",$connection);


header("Location: index.php?action=news");
exit();


}



if(isset($_POST["edit_news"])&& $_SESSION["group"]>=20){
$id=intval($_POST["edit_news"]);

$title=mysql_real_escape_string($_POST["title"]);
$activated=intval($_POST["activated"]);
$content=mysql_real_escape_string($_POST["news_content"]);
$date=time();
$autor=$_SESSION["login_id"];
$query=mysql_query("UPDATE ".tbname("news")." SET title='$title',content='$content',date=$date,active=$activated WHERE id=$id",$connection);


header("Location: index.php?action=news");
exit();


}



if($_GET["delete_news"]=="delete_news" && $_SESSION["group"]>=40){
$news = intval($_GET["news"]);
$query = mysql_query("DELETE FROM ".tbname("news")." WHERE id='$news'",$connection);
header("Location: index.php?action=news");
exit();
}


if($_POST["add_banner"] =="add_banner" && $_SESSION["group"]>=40){

$name = mysql_real_escape_string($_POST["banner_name"]);
$image_url = mysql_real_escape_string($_POST["image_url"]);
$link_url = mysql_real_escape_string($_POST["link_url"]);

$query = mysql_query("INSERT INTO ".tbname("banner")." 
(name,link_url,image_url) VALUES('$name','$link_url','$image_url')",$connection);

header("Location: index.php?action=banner");
exit();
}


if($_POST["add_key"]=="add_key" && $_SESSION["group"]>=40){

$name = mysql_real_escape_string($_POST["name"]);
$value = mysql_real_escape_string($_POST["value"]);

$query = mysql_query("INSERT INTO ".tbname("settings")." 
(name,value) VALUES('$name','$value')",$connection);

header("Location: index.php?action=settings");
exit();
}









if($_POST["add_admin"]=="add_admin" && $_SESSION["group"]>=50){

$username=mysql_real_escape_string($_POST["admin_username"]);
$lastname=mysql_real_escape_string($_POST["admin_lastname"]);
$firstname=mysql_real_escape_string($_POST["admin_firstname"]);
$email=mysql_real_escape_string($_POST["admin_email"]);
$password=mysql_real_escape_string($_POST["admin_password"]);
$query=mysql_query("INSERT INTO ".tbname("admins")." 
(username,lastname, firstname, email, password, `group`) VALUES('$username','$lastname','$firstname','$email','".md5($password)."',10)",$connection);
$message="Hallo $firstname,\n\n".
"Ein Administrator hat auf ".$_SERVER["SERVER_NAME"]." für dich ein neues Benutzerkonto angelegt.\n\n".
"Die Zugangsdaten lauten:\n\n".
"Benutzername: $username\n".
"Passwort: $password\n";
$header="From: ".env("email")."\n".
"Content-type: text/plain; charset=utf-8";

@mail($email, "Dein Benutzer-Account bei ".$_SERVER["SERVER_NAME"], $message, $header);

header("Location: index.php?action=admins");
exit();


}




if($_POST["edit_page"]=="edit_page" && $_SESSION["group"]>=30){
	$system_title = mysql_real_escape_string($_POST["page_"]);
	$page_title = mysql_real_escape_string($_POST["page_title"]);
	$activated = intval($_POST["activated"]);
	$page_content = mysql_real_escape_string($_POST["page_content"]);
	$comments_enabled = (int) $_POST["comments_enabled"];
	$redirection = mysql_real_escape_string($_POST["redirection"]);
	$notinfeed = (int)$_POST["notinfeed"];
	$menu = mysql_real_escape_string($_POST["menu"]);
	$position = (int)$_POST["position"];
	$parent = mysql_real_escape_string($_POST["parent"]);
	$user = $_SESSION["login_id"];     
  $id = intval($_POST["page_id"]);
  
  	mysql_query("UPDATE ".tbname("content")." SET systemname = '$system_title' , title='$page_title', parent='$parent', content='$page_content', active=$activated, lastmodified=".time().", comments_enabled=$comments_enabled, redirection = '$redirection', notinfeed = $notinfeed, menu = '$menu', position = $position, lastchangeby = $user WHERE id=$id",$connection);


  header("Location: index.php?action=pages");
  exit();

}





if($_POST["edit_admin"]=="edit_admin" && $_SESSION["group"]>=50){

$id=intval($_POST["id"]);
$username=mysql_real_escape_string($_POST["admin_username"]);
$lastname=mysql_real_escape_string($_POST["admin_lastname"]);
$firstname=mysql_real_escape_string($_POST["admin_firstname"]);
$email=mysql_real_escape_string($_POST["admin_email"]);
$password=mysql_real_escape_string($_POST["admin_password"]);
$rechte=mysql_real_escape_string($_POST["admin_rechte"]);
$query=mysql_query("UPDATE ".tbname("admins")." SET username='$username', `group`=$rechte, firstname='$firstname', lastname='$lastname', email='$email', password='".$password."' WHERE id=$id",$connection);


header("Location: index.php?action=admins");
exit();

}



if($_POST["edit_banner"]=="edit_banner" && $_SESSION["group"]>=40){
$name=mysql_real_escape_string($_POST["banner_name"]);
$image_url=mysql_real_escape_string($_POST["image_url"]);
$link_url=mysql_real_escape_string($_POST["link_url"]);
$id=intval($_POST["id"]);

$query=mysql_query("UPDATE ".tbname("banner")." 
SET name='$name',link_url='$link_url',image_url='$image_url' WHERE id=$id");


header("Location: index.php?action=banner");
exit();

}

if($_POST["edit_key"]=="edit_key" && $_SESSION["group"]>=50){
$name=mysql_real_escape_string($_POST["name"]);
$value=mysql_real_escape_string($_POST["value"]);
$id=intval($_POST["id"]);

$query=mysql_query("UPDATE ".tbname("settings")." 
SET name='$name',value='$value' WHERE id=$id");


header("Location: index.php?action=settings");
exit();

}
?>