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
  setconfig("items_in_rss_feed", intval($_POST["items_in_rss_feed"]));
  setconfig("logo_disabled", mysql_real_escape_string($_POST["logo_disabled"]));
  header("Location: index.php?action=settings_simple");
  exit();
}


if($_GET["action"]=="view_website" or $_GET["action"] == "frontpage"){
	header("Location: ../");
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



if(isset($_POST["add_menu_item"]) and $_SESSION["group"]>=50){
   $query = mysql_query("SELECT position FROM ".
   tbname("backend_menu_structure")." ORDER BY position DESC LIMIT 1");
   if(mysql_num_rows($query)>0){
    $fetched_assoc = mysql_fetch_assoc($query);
    $position = $fetched_assoc["position"] + 1;
   }else{
    $position = 1;
   }
   
   $action = mysql_real_escape_string($_POST["action"]);   
   $label = mysql_real_escape_string($_POST["label"]);
   
   mysql_query("INSERT INTO ".tbname("backend_menu_structure").
   "(action, label, position) 
   VALUES('$action', '$label', $position)");
}


if($_GET["action"] == "customize_menu" and
isset($_GET["delete"]) and
$_SESSION["group"]>=50){
  $delete = intval($_GET["delete"]);
  mysql_query("DELETE FROM ".tbname("backend_menu_structure").
  " WHERE position = $delete");
  mysql_query("UPDATE ".tbname("backend_menu_structure").
  " SET position = position - 1 WHERE position > $delete ");  
}


// Move Menu Item Up
if($_GET["action"] == "customize_menu" and isset($_GET["up"])
and $_SESSION["group"]>=50){                          
  $current_position = intval($_GET["up"]);                  
  if($current_position != 1){
  
        mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = -1".
    " WHERE position = $current_position");  
    
          mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = -2".
    " WHERE position = $current_position - 1");  
  
  
  
      mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = $current_position - 1".
    " WHERE position = -1");  
    
    
    mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = $current_position".
    " WHERE position = -2"); 
    
     

                                            
  
  } 
   
}



// Move Menu Item Down
if($_GET["action"] == "customize_menu" and isset($_GET["down"])
and $_SESSION["group"]>=50){
  $current_position = intval($_GET["down"]);
  
      $query = mysql_query("SELECT position FROM ".
   tbname("backend_menu_structure")." ORDER BY position DESC LIMIT 1");
   if(mysql_num_rows($query)>0){
    $fetched_assoc = mysql_fetch_assoc($query);
    $last_position = $fetched_assoc["position"];
   }else{
    $last_position = 1;
   }
  
  
  
  if($current_position != $last_position){
  

  
  
        mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = -1".
    " WHERE position = $current_position");  
    
          mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = -2".
    " WHERE position = $current_position + 1");  
  
  
  
      mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = $current_position + 1".
    " WHERE position = -1");  
    
    
    mysql_query("UPDATE ".
    tbname("backend_menu_structure")." SET position = $current_position".
    " WHERE position = -2"); 
    
     

                                            
  
  } 
   
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
	$access = implode(",", $_POST["access"]);
	$access = mysql_real_escape_string($access);
	$meta_description = mysql_real_escape_string($_POST["meta_description"]); 
	$meta_keywords = mysql_real_escape_string($_POST["meta_keywords"]);
	
	mysql_query("INSERT INTO ".tbname("content").
	" (systemname,title,content,parent, active,created,lastmodified,autor,
  comments_enabled,notinfeed,redirection,menu,position, access, meta_description, meta_keywords) 
  VALUES('$system_title','$page_title','$page_content','$parent', $activated,".time().", ".time().
  ",".$_SESSION["login_id"].
  ", ".$comments_enabled .
  ",$notinfeed, '$redirection', '$menu', $position, '".$access."', '$meta_description', '$meta_keywords')",$connection)or die(mysql_error());


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
	$access = implode(",", $_POST["access"]);
	$access = mysql_real_escape_string($access);
	$meta_description = mysql_real_escape_string($_POST["meta_description"]); 
	$meta_keywords = mysql_real_escape_string($_POST["meta_keywords"]);
  
  	mysql_query("UPDATE ".tbname("content")." SET systemname = '$system_title' , title='$page_title', parent='$parent', content='$page_content', active=$activated, lastmodified=".time().", comments_enabled=$comments_enabled, redirection = '$redirection', notinfeed = $notinfeed, menu = '$menu', position = $position, lastchangeby = $user, access = '$access', meta_description = '$meta_description', meta_keywords = '$meta_keywords' WHERE id=$id",$connection);


  header("Location: index.php?action=pages");
  exit();

}



// Resize image
function resize_image($file, $target, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*($r-$w/$h)));
        } else {
            $height = ceil($height-($height*($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
	
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
	
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	imagejpeg($dst, $target, 100);
  
}



// Logo Upload
  if(!empty($_FILES['logo_upload_file']['name'])
  and $_SESSION["group"] >= 40){
    if(!file_exists("../content/images")){ 
      @mkdir("../content/images");
      @chmod("../content/images", 0777);
  
  }
 
 
  $logo_upload = $_FILES['logo_upload_file'];
  $type =  $logo_upload['type'];
  $filename =  $logo_upload['name'];
  $extension = file_extension($filename); 
  $hash = md5(file_get_contents($logo_upload['tmp_name']));
  if($type == "image/jpeg" or 
   $type == "image/jpg" or
   $type == "image/png" or
   $type == "image/gif"){
                  
   $new_filename =  "../content/images/". $hash.".".$extension;
   $logo_upload_filename = $hash.".".$extension;
   resize_image($logo_upload['tmp_name'], $new_filename ,
   500, 100, $crop=FALSE); 
   setconfig("logo_image", $logo_upload_filename);
   }
  
  
  
  
}



if($_POST["edit_admin"]=="edit_admin" && $_SESSION["group"]>=50
or ($_POST["edit_admin"]=="edit_admin" and $_SESSION["group"]>=10 and $_POST["id"] == $_SESSION["login_id"])){

$id = intval($_POST["id"]);
if(!empty($_FILES['avatar_upload']['name'])){
if(!file_exists("../content/avatars")){ 
  @mkdir("../content/avatars");
  @chmod("../content/avatars", 0777);
  
}
 
  $avatar_upload = $_FILES['avatar_upload'];
  $type = $avatar_upload['type'];
  $filename = $avatar_upload['name'];
  $extension = file_extension($filename); 
  $hash = md5(file_get_contents($avatar_upload['tmp_name']));
  if($type == "image/jpeg" or 
   $type == "image/jpg" or
   $type == "image/png" or
   $type == "image/gif"){
   
   $new_filename =  "../content/avatars/". $hash.".".$extension;
   $db_avatar_filename = $hash.".".$extension;
      resize_image($avatar_upload['tmp_name'], $new_filename ,
      125, 125, $crop=FALSE); 
   }
}

if($db_avatar_filename == "content/avatars/"){
  $db_avatar_filename = "";
}

$username = mysql_real_escape_string($_POST["admin_username"]);
$lastname = mysql_real_escape_string($_POST["admin_lastname"]);
$firstname = mysql_real_escape_string($_POST["admin_firstname"]);
$email = mysql_real_escape_string($_POST["admin_email"]);
$password = mysql_real_escape_string($_POST["admin_password"]);
$rechte = mysql_real_escape_string($_POST["admin_rechte"]);
$icq_id = mysql_real_escape_string($_POST["icq_id"]);        
$icq_id = mysql_real_escape_string($_POST["icq_id"]);  
$skype_id = mysql_real_escape_string($_POST["skype_id"]);     
$about_me = mysql_real_escape_string($_POST["about_me"]);  
mysql_query("UPDATE ".tbname("admins")." SET username='$username', `group`= $rechte, firstname='$firstname',
lastname='$lastname', email='$email', password='".$password."',
`icq_id`='$icq_id',  skype_id = '$skype_id',
about_me = '$about_me', avatar_file = '$db_avatar_filename' WHERE id=$id",$connection);


if($_SESSION["group"]>=10 and $_POST["id"] == $_SESSION["login_id"]){
   header("Location: index.php");
   exit();
}else{
  header("Location: index.php?action=admins");
  exit();
}

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