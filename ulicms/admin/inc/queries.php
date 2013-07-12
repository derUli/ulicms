<?php


add_hook("query");

if($_GET["action"] == "save_settings" && isset($_POST["save_settings"])){
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
     setconfig("logo_disabled", mysql_real_escape_string($_POST["logo_disabled"]));
     setconfig("timezone", mysql_real_escape_string($_POST["timezone"]));
     setconfig("robots", mysql_real_escape_string($_POST["robots"]));
    
     if($_POST["disable_html_validation"] == "enabled")
         deleteconfig("disable_html_validation");
     else
         setconfig("disable_html_validation", "disable");
    
     header("Location: index.php?action=settings_simple");
     exit();
     }




if($_GET["action"] == "view_website" or $_GET["action"] == "frontpage"){
     header("Location: ../");
     exit();
     }


if(isset($_GET["clear_cache"])){
     SureRemoveDir("../content/cache", false);
     }


if($_GET["action"] == "undelete_page" && $_SESSION["group"] >= 40){
     $page = intval($_GET["page"]);
     db_query("UPDATE " . tbname("content") . " SET `deleted_at` = NULL" .
         " WHERE id=$page");
     header("Location: index.php?action=pages");
     exit();
    
     }

if($_GET["action"] == "pages_delete" && $_SESSION["group"] >= 40){
     $page = intval($_GET["page"]);
     db_query("UPDATE " . tbname("content") . " SET `deleted_at` = " . time() .
         " WHERE id=$page");
     header("Location: index.php?action=pages");
     exit();
     }

if($_GET["action"] == "spam_filter" and
     isset($_POST["submit_spamfilter_settings"])){
    
    
     if($_POST["spamfilter_enabled"] == "yes"){
         setconfig("spamfilter_enabled", "yes");
         }
    else{
         setconfig("spamfilter_enabled", "no");
         }
    
     if(isset($_POST["country_blacklist"])){
         setconfig("country_blacklist",
             $_POST["country_blacklist"]);
         }
    
    
    
    
     if(isset($_POST["spamfilter_words_blacklist"])){
         $blacklist = $_POST["spamfilter_words_blacklist"];
         $blacklist = str_replace("\r\n", "||", $blacklist);
         $blacklist = str_replace("\n", "||", $blacklist);
         setconfig("spamfilter_words_blacklist", $blacklist);
         }
    
     }



if(!empty($_POST["save_template"]) && !empty($_POST["code"]) && $_SESSION["group"] >= 40){
     $theme = getconfig("theme");
     $save = getTemplateDirPath($theme) . basename($_POST["save_template"]);
     if(is_file($save) && is_writable($save)){
         $handle = fopen($save, "w");
         fwrite($handle, $_POST["code"]);
         fclose($handle);
         header("Location: index.php?action=templates&save=true");
         exit();
         }else{
        
         header("Location: index.php?action=templates&save=false");
         exit();
         }
    
     }

if($_SESSION["group"] >= 40 and
     $_GET["action"] == "empty_trash"){
     db_query("DELETE FROM " . tbname("content") . " WHERE deleted_at IS NOT NULL");
     header("Location: index.php?action=pages");
     exit();
     }


if($_GET["action"] == "key_delete" && $_SESSION["group"] >= 40){
     deleteconfig($_GET["key"]);
     header("Location: index.php?action=settings");
     exit();
     }

if($_GET["action"] == "languages" and !empty($_GET["delete"]) and $_SESSION["group"] >= 50){
     db_query("DELETE FROM " . tbname("languages") . " WHERE id = " . intval($_GET["delete"]));
    
    
     }

if($_GET["action"] == "languages" and !empty($_GET["default"]) and $_SESSION["group"] >= 50){
     setconfig("default_language", $_GET["default"]);
     }


if(isset($_POST["add_language"]) and $_SESSION["group"] >= 50){
     if(!empty($_POST["name"]) and !empty($_POST["language_code"])){
         $name = mysql_real_escape_string($_POST["name"]);
         $language_code = mysql_real_escape_string($_POST["language_code"]);
         db_query("INSERT INTO " . tbname("languages") .
             "(name, language_code)
      VALUES('$name', '$language_code')");
         }
     }


if(isset($_POST["add_menu_item"]) and $_SESSION["group"] >= 50){
     $query = db_query("SELECT position FROM " .
         tbname("backend_menu_structure") . " ORDER BY position DESC LIMIT 1");
     if(mysql_num_rows($query) > 0){
         $fetched_assoc = mysql_fetch_assoc($query);
         $position = $fetched_assoc["position"] + 1;
         }else{
         $position = 1;
         }
    
     $action = mysql_real_escape_string($_POST["action"]);
     $label = mysql_real_escape_string($_POST["label"]);
    
     db_query("INSERT INTO " . tbname("backend_menu_structure") .
         "(action, label, position) 
   VALUES('$action', '$label', $position)");
     }


if($_GET["action"] == "customize_menu" and
     isset($_GET["delete"]) and
         $_SESSION["group"] >= 50){
     $delete = intval($_GET["delete"]);
     db_query("DELETE FROM " . tbname("backend_menu_structure") .
         " WHERE position = $delete");
     db_query("UPDATE " . tbname("backend_menu_structure") .
         " SET position = position - 1 WHERE position > $delete ");
     }


// Move Menu Item Up
if($_GET["action"] == "customize_menu" and isset($_GET["up"])
         and $_SESSION["group"] >= 50){
     $current_position = intval($_GET["up"]);
     if($current_position != 1){
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = -1" .
             " WHERE position = $current_position");
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = -2" .
             " WHERE position = $current_position - 1");
        
        
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = $current_position - 1" .
             " WHERE position = -1");
        
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = $current_position" .
             " WHERE position = -2");
        
        
        
        
        
         }
    
     }



// Move Menu Item Down
if($_GET["action"] == "customize_menu" and isset($_GET["down"])
         and $_SESSION["group"] >= 50){
     $current_position = intval($_GET["down"]);
    
     $query = db_query("SELECT position FROM " .
         tbname("backend_menu_structure") . " ORDER BY position DESC LIMIT 1");
     if(mysql_num_rows($query) > 0){
         $fetched_assoc = mysql_fetch_assoc($query);
         $last_position = $fetched_assoc["position"];
         }else{
         $last_position = 1;
         }
    
    
    
     if($current_position != $last_position){
        
        
        
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = -1" .
             " WHERE position = $current_position");
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = -2" .
             " WHERE position = $current_position + 1");
        
        
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = $current_position + 1" .
             " WHERE position = -1");
        
        
         db_query("UPDATE " .
             tbname("backend_menu_structure") . " SET position = $current_position" .
             " WHERE position = -2");
        
        
        
        
        
         }
    
     }








if($_GET["action"] == "banner_delete" && $_SESSION["group"] >= 40){
     $banner = intval($_GET["banner"]);
     $query = db_query("DELETE FROM " . tbname("banner") . " WHERE id='$banner'", $connection);
     header("Location: index.php?action=banner");
     exit();
     }


if($_GET["action"] == "admin_delete" && $_SESSION["group"] >= 40){
     $admin = intval($_GET["admin"]);
     $query = db_query("DELETE FROM " . tbname("admins") . " WHERE id='$admin'", $connection);
     header("Location: index.php?action=admins");
     exit();
     }


if($_POST["add_page"] == "add_page"){
     if($_POST["system_title"] != ""){
        
         $system_title = mysql_real_escape_string($_POST["system_title"]);
         $page_title = mysql_real_escape_string($_POST["page_title"]);
         $activated = intval($_POST["activated"]);
         $page_content = $_POST["page_content"];
         $comments_enabled = (int)$_POST["comments_enabled"];
         $notinfeed = 0;
         $redirection = mysql_real_escape_string($_POST["redirection"]);
         $menu = mysql_real_escape_string($_POST["menu"]);
         $position = (int)$_POST["position"];
        
         if($_POST["parent"] == "NULL")
             $parent = "NULL";
         else
             $parent = mysql_real_escape_string($_POST["parent"]);
         $access = implode(",", $_POST["access"]);
         $access = mysql_real_escape_string($access);
         $target = mysql_real_escape_string($_POST["target"]);
         $meta_description = $_POST["meta_description"];
         $meta_keywords = $_POST["meta_keywords"];
        
         if(empty($meta_description) and
                 !getconfig("disable_auto_generate_meta_tags")){
             include_once "../lib/string_functions.php";
             $maxlength_chars = 160;
            
             $shortContent = strip_tags($page_content);
            
             // Leerzeichen und Zeilenumbrüche entfernen
            $shortContent = trim($shortContent);
             $shortContent = preg_replace("#[ ]*[\r\n\v]+#", "\r\n", $shortContent);
             $shortContent = preg_replace("#[ \t]+#", " ", $shortContent);
             $shortContent = str_replace("\r\n", " ", $shortContent);
             $shortContent = str_replace("\n", " ", $shortContent);
             $shortContent = str_replace("\r", " ", $shortContent);
             $shortContent = trim($shortContent);
             $shortstring = $shortContent;
             $word_count = str_word_count($shortstring);
            
             while(strlen($shortstring) > 160){
                 $shortstring = getExcerpt($shortContent, 0, $word_count);
                 $word_count -= 1;
                 }
            
            
             $meta_description = $shortstring;
            
             }
        
         $meta_description = mysql_real_escape_string($meta_description);
        
        
         // Wenn keine Meta Keywords gesetzt sind selbst ermitteln
        if(empty($meta_keywords) and
                 !getconfig("disable_auto_generate_meta_tags")){
            
             include_once "../lib/string_functions.php";
             $stripped_content = trim($page_content);
             $stripped_content = str_replace("\\r\\n", "\r\n", $stripped_content);
             $stripped_content = strip_tags($stripped_content);
             $words = keywordsFromString($stripped_content);
             $maxWords = 10;
             $currentWordCount = 0;
             $maxi = count($words);
             $i = 0;
             $meta_keywords = Array();
             if(count($words) > 0){
                 foreach ($words as $key => $value){
                     $i++;
                     $key = trim($key);
                     if(!empty($key) and $currentWordCount < $maxWords){
                         $currentWordCount++;
                         array_push($meta_keywords, $key);
                         }
                    
                    
                    
                     }
                
                 $meta_keywords = implode(", ", $meta_keywords);
                
                 }
            
             }
        
         $page_content = mysql_real_escape_String($page_content);
         $meta_keywords = mysql_real_escape_string($meta_keywords);
        
         $language = mysql_real_escape_string($_POST["language"]);
        
         db_query("INSERT INTO " . tbname("content") .
             " (systemname,title,content,parent, active,created,lastmodified,autor,
  comments_enabled,notinfeed,redirection,menu,position, 
  access, meta_description, meta_keywords, language, target) 
  VALUES('$system_title','$page_title','$page_content',$parent, $activated," . time() . ", " . time() .
             "," . $_SESSION["login_id"] .
             ", " . $comments_enabled .
             ",$notinfeed, '$redirection', '$menu', $position, '" . $access . "', 
  '$meta_description', '$meta_keywords',
  '$language', '$target')");
        
         // header("Location: index.php?action=pages_edit&page=".mysql_insert_id()."#bottom");
        header("Location: index.php?action=pages");
         exit();
        
         }
    
     }



if($_POST["add_news"] == "add_news" && $_SESSION["group"] >= 20){
     $title = mysql_real_escape_string($_POST["title"]);
     $activated = intval($_POST["activated"]);
     $content = mysql_real_escape_string($_POST["news_content"]);
     $date = time();
     $autor = $_SESSION["login_id"];
     $query = db_query("INSERT INTO " . tbname("news") . " 
(title,content,active, autor,date) VALUES('$title','$content',$activated,$autor,$date)", $connection);
    
    
     header("Location: index.php?action=news");
     exit();
    
    
     }



if(isset($_POST["edit_news"]) && $_SESSION["group"] >= 20){
     $id = intval($_POST["edit_news"]);
    
     $title = mysql_real_escape_string($_POST["title"]);
     $activated = intval($_POST["activated"]);
     $content = mysql_real_escape_string($_POST["news_content"]);
     $date = time();
     $autor = $_SESSION["login_id"];
     $query = db_query("UPDATE " . tbname("news") . " SET title='$title',content='$content',date=$date,active=$activated WHERE id=$id", $connection);
    
    
     header("Location: index.php?action=news");
     exit();
    
    
     }



if($_GET["delete_news"] == "delete_news" && $_SESSION["group"] >= 40){
     $news = intval($_GET["news"]);
     $query = db_query("DELETE FROM " . tbname("news") . " WHERE id='$news'", $connection);
     header("Location: index.php?action=news");
     exit();
     }


if($_POST["add_banner"] == "add_banner" && $_SESSION["group"] >= 40){
    
     $name = mysql_real_escape_string($_POST["banner_name"]);
     $image_url = mysql_real_escape_string($_POST["image_url"]);
     $link_url = mysql_real_escape_string($_POST["link_url"]);
    
     $query = db_query("INSERT INTO " . tbname("banner") . " 
(name,link_url,image_url) VALUES('$name','$link_url','$image_url')", $connection);
    
     header("Location: index.php?action=banner");
     exit();
     }


if($_POST["add_key"] == "add_key" && $_SESSION["group"] >= 40){
    
     $name = mysql_real_escape_string($_POST["name"]);
     $value = mysql_real_escape_string($_POST["value"]);
    
     $query = db_query("INSERT INTO " . tbname("settings") . " 
(name,value) VALUES('$name','$value')", $connection);
    
     header("Location: index.php?action=settings");
     exit();
     }









if($_POST["add_admin"] == "add_admin" && $_SESSION["group"] >= 50){
     include "../lib/encryption.php";
     $username = mysql_real_escape_string($_POST["admin_username"]);
     $lastname = mysql_real_escape_string($_POST["admin_lastname"]);
     $firstname = mysql_real_escape_string($_POST["admin_firstname"]);
     $email = mysql_real_escape_string($_POST["admin_email"]);
     $password = mysql_real_escape_string($_POST["admin_password"]);
     db_query("INSERT INTO " . tbname("admins") . " 
(username,lastname, firstname, email, password, `group`) VALUES('$username','$lastname','$firstname','$email','" . hash_password($password) . "',10)", $connection);
     $message = "Hallo $firstname,\n\n" .
     "Ein Administrator hat auf " . $_SERVER["SERVER_NAME"] . " für dich ein neues Benutzerkonto angelegt.\n\n" .
     "Die Zugangsdaten lauten:\n\n" .
     "Benutzername: $username\n" .
     "Passwort: $password\n";
     $header = "From: " . env("email") . "\n" .
     "Content-type: text/plain; charset=utf-8";
    
     @mail($email, "Dein Benutzer-Account bei " . $_SERVER["SERVER_NAME"], $message, $header);
    
     header("Location: index.php?action=admins");
     exit();
    
    
     }




if($_POST["edit_page"] == "edit_page" && $_SESSION["group"] >= 30){
     $system_title = mysql_real_escape_string($_POST["page_"]);
     $page_title = mysql_real_escape_string($_POST["page_title"]);
     $activated = intval($_POST["activated"]);
     $page_content = mysql_real_escape_string($_POST["page_content"]);
     $comments_enabled = (int) $_POST["comments_enabled"];
     $redirection = mysql_real_escape_string($_POST["redirection"]);
     $notinfeed = 0;
     $menu = mysql_real_escape_string($_POST["menu"]);
     $position = (int)$_POST["position"];
    
     $parent = "NULL";
     if($_POST["parent"] != "NULL"){
         $parent = intval($_POST["parent"]);
         }
    
     $user = $_SESSION["login_id"];
     $id = intval($_POST["page_id"]);
     $access = implode(",", $_POST["access"]);
     $access = mysql_real_escape_string($access);
     $target = mysql_real_escape_string($_POST["target"]);
     $meta_description = mysql_real_escape_string($_POST["meta_description"]);
     $meta_keywords = mysql_real_escape_string($_POST["meta_keywords"]);
    
     db_query("UPDATE " . tbname("content") . " SET systemname = '$system_title' , title='$page_title', parent=$parent, content='$page_content', active=$activated, lastmodified=" . time() . ", comments_enabled=$comments_enabled, redirection = '$redirection', notinfeed = $notinfeed, menu = '$menu', position = $position, lastchangeby = $user, access = '$access', meta_description = '$meta_description', meta_keywords = '$meta_keywords', target='$target' WHERE id=$id");
    
    
     header("Location: index.php?action=pages");
     exit();
    
     }



// Resize image
function resize_image($file, $target, $w, $h, $crop = FALSE){
     list($width, $height) = getimagesize($file);
     $r = $width / $height;
     if ($crop){
         if ($width > $height){
             $width = ceil($width - ($width * ($r - $w / $h)));
             }else{
             $height = ceil($height - ($height * ($r - $w / $h)));
             }
         $newwidth = $w;
         $newheight = $h;
         }else{
         if ($w / $h > $r){
             $newwidth = $h * $r;
             $newheight = $h;
             }else{
             $newheight = $w / $r;
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
     $type = $logo_upload['type'];
     $filename = $logo_upload['name'];
     $extension = file_extension($filename);
    
    
     if($type == "image/jpeg" or
         $type == "image/jpg" or
         $type == "image/gif" or
         $type == "image/png"){
        
         $hash = md5(file_get_contents($logo_upload['tmp_name']));
         $new_filename = "../content/images/" . $hash . "." . $extension;
         $logo_upload_filename = $hash . "." . $extension;
        
         move_uploaded_file($logo_upload['tmp_name'], $new_filename);
         $image_size = getimagesize($new_filename);
         if($image_size[0] <= 500 and $image_size[1] <= 100){
             setconfig("logo_image", $logo_upload_filename);
            
             }else{
             header("Location: index.php?action=logo_upload&error=to_big");
             exit();
             }
        
         }
    
    
    
    
     }



if($_POST["edit_admin"] == "edit_admin" && $_SESSION["group"] >= 50
     or ($_POST["edit_admin"] == "edit_admin" and $_SESSION["group"] >= 10
         and $_POST["id"] == $_SESSION["login_id"])){
    
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
        
         if($type == "image/jpeg" or $type == "image/jpg"){
            
             $new_filename = "../content/avatars/" . $hash . ".jpg";
            
             $db_avatar_filename = $hash . ".jpg";
             resize_image($avatar_upload['tmp_name'], $new_filename ,
                 125, 125, $crop = FALSE);
            
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
     $skype_id = mysql_real_escape_string($_POST["skype_id"]);
     $about_me = mysql_real_escape_string($_POST["about_me"]);
     db_query("UPDATE " . tbname("admins") . " SET username = '$username', `group`= $rechte, firstname='$firstname',
lastname='$lastname', email='$email', 
`icq_id`='$icq_id', skype_id = '$skype_id',
about_me = '$about_me', avatar_file = '$db_avatar_filename' WHERE id=$id", $connection);
    
     if(!empty($password))
         changePassword($password, $id);
    
     if($_SESSION["group"] >= 10 and $_POST["id"] == $_SESSION["login_id"]){
         header("Location: index.php");
         exit();
         }else{
         header("Location: index.php?action=admins");
         exit();
         }
    
     }



if($_POST["edit_banner"] == "edit_banner" && $_SESSION["group"] >= 40){
     $name = mysql_real_escape_string($_POST["banner_name"]);
     $image_url = mysql_real_escape_string($_POST["image_url"]);
     $link_url = mysql_real_escape_string($_POST["link_url"]);
     $id = intval($_POST["id"]);
    
     $query = db_query("UPDATE " . tbname("banner") . " 
SET name='$name',link_url='$link_url',image_url='$image_url' WHERE id=$id");
    
    
     header("Location: index.php?action=banner");
     exit();
    
     }

if($_POST["edit_key"] == "edit_key" && $_SESSION["group"] >= 50){
     $name = mysql_real_escape_string($_POST["name"]);
     $value = mysql_real_escape_string($_POST["value"]);
     $id = intval($_POST["id"]);
    
     $query = db_query("UPDATE " . tbname("settings") . " 
SET name='$name',value='$value' WHERE id=$id");
    
    
     header("Location: index.php?action=settings");
     exit();
    
     }
?>
