<?php

$acl = new ACL();
add_hook("query");

if($_GET["action"] == "save_settings" && isset($_POST["save_settings"])){
     add_hook("before_safe_simple_settings");
     setconfig("registered_user_default_level", intval($_POST["registered_user_default_level"]));
     setconfig("homepage_owner", db_escape($_POST["homepage_owner"]));
     setconfig("language", db_escape($_POST["language"]));
     setconfig("visitors_can_register", intval(isset($_POST["visitors_can_register"])));
     setconfig("maintenance_mode", intval(isset($_POST["maintenance_mode"])));
     setconfig("email", db_escape($_POST["email"]));
     setconfig("max_news", (int)$_POST["max_news"]);
     setconfig("comment_mode", db_escape($_POST["comment_mode"]));
     setconfig("disqus_id", db_escape($_POST["disqus_id"]));
     setconfig("facebook_id", db_escape($_POST["facebook_id"]));
     setconfig("logo_disabled", db_escape($_POST["logo_disabled"]));
     setconfig("timezone", db_escape($_POST["timezone"]));
     setconfig("robots", db_escape($_POST["robots"]));
    
     if($_POST["disable_html_validation"] == "enabled")
         deleteconfig("disable_html_validation");
     else
         setconfig("disable_html_validation", "disable");
    
     if(!isset($_POST["disable_password_reset"]))
        setconfig("disable_password_reset", "disable_password_reset");
     else
        deleteconfig("disable_password_reset");
        
     add_hook("after_safe_simple_settings");
     header("Location: index.php?action=settings_simple");
     exit();
     }




if($_GET["action"] == "view_website" or $_GET["action"] == "frontpage"){
     add_hook("before_view_website");
     header("Location: ../");
     exit();
     }


if(isset($_GET["clear_cache"])){
     clearCache();
     }


if($_GET["action"] == "undelete_page" && $acl -> hasPermission("pages")){
     $page = intval($_GET["page"]);
     add_hook("before_undelete_page");
     db_query("UPDATE " . tbname("content") . " SET `deleted_at` = NULL" .
         " WHERE id=$page");
     add_hook("after_undelete_page");
     header("Location: index.php?action=pages");
     exit();
    
     }

if($_GET["action"] == "pages_delete" && $acl -> hasPermission("pages")){
     $page = intval($_GET["page"]);
     add_hook("before_delete_page");
     db_query("UPDATE " . tbname("content") . " SET `deleted_at` = " . time() .
         " WHERE id=$page");
     add_hook("after_delete_page");
     header("Location: index.php?action=pages");
     exit();
     }

if($_GET["action"] == "spam_filter" and
     isset($_POST["submit_spamfilter_settings"])){
    
     add_hook("before_save_spamfilter_settings");
    
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
    
    
    
     if(isset($_POST["check_for_spamhaus"])){
         setconfig("check_for_spamhaus", "check");
         }
    else{
         deleteconfig("check_for_spamhaus");
         }
    
    
     if(isset($_POST["spamfilter_words_blacklist"])){
         $blacklist = $_POST["spamfilter_words_blacklist"];
         $blacklist = str_replace("\r\n", "||", $blacklist);
         $blacklist = str_replace("\n", "||", $blacklist);
         setconfig("spamfilter_words_blacklist", $blacklist);
         }
    
     if(isset($_POST["disallow_chinese_chars"]))
         setconfig("disallow_chinese_chars", "disallow");
     else
         deleteconfig("disallow_chinese_chars");
     add_hook("after_save_spamfilter_settings");
     }



if(!empty($_POST["save_template"]) and !empty($_POST["code"]) && $acl -> hasPermission("templates")){
     $theme = getconfig("theme");
     $save = getTemplateDirPath($theme) . basename($_POST["save_template"]);
     add_hook("before_save_template");
     if(is_file($save) && is_writable($save)){
         $handle = fopen($save, "w");
         fwrite($handle, $_POST["code"]);
         fclose($handle);
        
         add_hook("after_save_template");
         add_hook("after_save_template_successfull");
         header("Location: index.php?action=templates&save=true");
         exit();
         }else{
        
         add_hook("after_save_template");
         add_hook("after_save_template_failed");
         header("Location: index.php?action=templates&save=false");
         exit();
         }
    
     }

if($_GET["action"] == "empty_trash"){
     add_hook("before_empty_trash");
     db_query("DELETE FROM " . tbname("content") . " WHERE deleted_at IS NOT NULL");
     add_hook("after_empty_trash");
     header("Location: index.php?action=pages");
     exit();
     }


if($_GET["action"] == "key_delete" and $acl -> hasPermission("expert_settings")){
     add_hook("before_delete_key");
     deleteconfig($_GET["key"]);
     add_hook("after_delete_key");
     header("Location: index.php?action=settings");
     exit();
     }

if($_GET["action"] == "languages" and !empty($_GET["delete"]) and $acl -> hasPermission("languages")){
     add_hook("before_delete_language");
     db_query("DELETE FROM " . tbname("languages") . " WHERE id = " . intval($_GET["delete"]));
     add_hook("after_delete_language");
    
     }

if($_GET["action"] == "languages" and !empty($_GET["default"]) and $acl -> hasPermission("languages")){
     add_hook("before_set_default_language");
     setconfig("default_language", db_escape($_GET["default"]));
     setconfig("system_language", db_escape($_GET["default"]));
     add_hook("after_set_default_language");
     }


if(isset($_POST["add_language"]) and $acl -> hasPermission("languages")){
     if(!empty($_POST["name"]) and !empty($_POST["language_code"])){
         $name = db_escape($_POST["name"]);
         $language_code = db_escape($_POST["language_code"]);
         add_hook("before_create_language");
         db_query("INSERT INTO " . tbname("languages") .
             "(name, language_code)
      VALUES('$name', '$language_code')");
         add_hook("after_create_language");
         }
     }

if($_GET["action"] == "banner_delete" && $acl -> hasPermission("banners")){
     $banner = intval($_GET["banner"]);
    
     add_hook("before_banner_delete");
     $query = db_query("DELETE FROM " . tbname("banner") . " WHERE id='$banner'", $connection);
     add_hook("after_banner_delete");
     header("Location: index.php?action=banner");
     exit();
     }


if($_GET["action"] == "admin_delete" && (is_admin() or $acl -> hasPermission("users"))){
     $admin = intval($_GET["admin"]);
     add_hook("before_admin_delete");
     $query = db_query("DELETE FROM " . tbname("users") . " WHERE id='$admin'", $connection);
     add_hook("after_admin_delete");
     header("Location: index.php?action=admins");
     exit();
     }


if($_POST["add_page"] == "add_page" && $acl -> hasPermission("pages")){
     if($_POST["system_title"] != ""){
        
         $system_title = db_escape($_POST["system_title"]);
         $page_title = db_escape($_POST["page_title"]);
         $alternate_title = db_escape($_POST["alternate_title"]);
         $activated = intval($_POST["activated"]);
         $page_content = $_POST["page_content"];
         $comments_enabled = (int)$_POST["comments_enabled"];
         $category = intval($_POST["category"]);
         $notinfeed = 0;
         $redirection = db_escape($_POST["redirection"]);
         $html_file = db_escape($_POST["html_file"]);
         $menu = db_escape($_POST["menu"]);
         $position = (int)$_POST["position"];
         $menu_image = db_escape($_POST["menu_image"]);
         $custom_data = db_escape($_POST["custom_data"]);
         $theme = db_escape($_POST["theme"]);
        
         if($_POST["parent"] == "NULL")
             $parent = "NULL";
         else
             $parent = db_escape($_POST["parent"]);
         $access = implode(",", $_POST["access"]);
         $access = db_escape($access);
         $target = db_escape($_POST["target"]);
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
        
        
         $meta_description = unhtmlspecialchars($meta_description);
        
         $meta_description = db_escape($meta_description);
        
        
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
        
         $page_content = db_real_escape_String($page_content);
         $meta_keywords = unhtmlspecialchars($meta_keywords);
         $meta_keywords = db_escape($meta_keywords);
        
         $language = db_escape($_POST["language"]);
        
         add_hook("before_create_page");
         db_query("INSERT INTO " . tbname("content") .
             " (systemname,title,content,parent, active,created,lastmodified,autor,
  comments_enabled,notinfeed,redirection,menu,position, 
  access, meta_description, meta_keywords, language, target, category, `html_file`, `alternate_title`, `menu_image`, `custom_data`, `theme`) 
  VALUES('$system_title','$page_title','$page_content',$parent, $activated," . time() . ", " . time() .
             "," . $_SESSION["login_id"] .
             ", " . $comments_enabled .
             ",$notinfeed, '$redirection', '$menu', $position, '" . $access . "', 
  '$meta_description', '$meta_keywords',
  '$language', '$target', '$category', '$html_file', '$alternate_title', '$menu_image', '$custom_data', '$theme')")or die(db_error());
        
         add_hook("after_create_page");
         // header("Location: index.php?action=pages_edit&page=".db_insert_id()."#bottom");
        header("Location: index.php?action=pages");
         exit();
        
         }
    
     }

if($_POST["add_banner"] == "add_banner" && $acl -> hasPermission("banners")){
    
     $name = db_escape($_POST["banner_name"]);
     $image_url = db_escape($_POST["image_url"]);
     $link_url = db_escape($_POST["link_url"]);
     $category = intval($_POST["category"]);
     $type = db_escape($_POST["type"]);
     $html = db_escape($_POST["html"]);
     $language = db_escape($_POST["language"]);
    
     add_hook("before_create_banner");
     $query = db_query("INSERT INTO " . tbname("banner") . " 
(name,link_url,image_url, category, `type`, html, `language`) VALUES('$name','$link_url','$image_url', '$category', '$type', '$html',
'$language')", $connection);
    
     add_hook("after_create_banner");
     header("Location: index.php?action=banner");
     exit();
     }


if($_POST["add_key"] == "add_key" and $acl -> hasPermission("expert_settings")){
    
     $name = db_escape($_POST["name"]);
     $value = db_escape($_POST["value"]);
     add_hook("before_add_key");
     $query = db_query("INSERT INTO " . tbname("settings") . " 
(name,value) VALUES('$name','$value')", $connection);
    
     add_hook("after_add_key");
     header("Location: index.php?action=settings");
     exit();
     }

if($_POST["add_admin"] == "add_admin" && (is_admin() or $acl -> hasPermission("users"))){
     $username = $_POST["admin_username"];
     $lastname = $_POST["admin_lastname"];
     $firstname = $_POST["admin_firstname"];
     $group = 40;
     $password = $_POST["admin_password"];
     $email = $_POST["admin_email"];
     $sendMail = isset($_POST["send_mail"]);
     adduser($username, $lastname, $firstname, $email, $password, $group, $sendMail);
     header("Location: index.php?action=admins");
     exit();
    
    
     }




if($_POST["edit_page"] == "edit_page" && $acl -> hasPermission("pages")){
     $system_title = db_escape($_POST["page_"]);
     $page_title = db_escape($_POST["page_title"]);
     $activated = intval($_POST["activated"]);
     $page_content = db_escape($_POST["page_content"]);
     $comments_enabled = (int) $_POST["comments_enabled"];
     $category = intval($_POST["category"]);
     $redirection = db_escape($_POST["redirection"]);
     $notinfeed = 0;
     $menu = db_escape($_POST["menu"]);
     $position = (int)$_POST["position"];
     $html_file = db_escape($_POST["html_file"]);
     $menu_image = db_escape($_POST["menu_image"]);
     $custom_data = db_escape($_POST["custom_data"]);
     $theme = db_escape($_POST["theme"]);
    
     $alternate_title = db_escape($_POST["alternate_title"]);
    
     $parent = "NULL";
     if($_POST["parent"] != "NULL"){
         $parent = intval($_POST["parent"]);
         }
    
     $user = $_SESSION["login_id"];
     $id = intval($_POST["page_id"]);
     $access = implode(",", $_POST["access"]);
     $access = db_escape($access);
     $target = db_escape($_POST["target"]);
     $meta_description = db_escape($_POST["meta_description"]);
     $meta_keywords = db_escape($_POST["meta_keywords"]);
     $language = db_escape($_POST["language"]);
     add_hook("before_edit_page");
     db_query("UPDATE " . tbname("content") . " SET `html_file` = '$html_file', systemname = '$system_title' , title='$page_title', `alternate_title`='$alternate_title', parent=$parent, content='$page_content', active=$activated, lastmodified=" . time() . ", comments_enabled=$comments_enabled, redirection = '$redirection', notinfeed = $notinfeed, menu = '$menu', position = $position, lastchangeby = $user, language='$language', access = '$access', meta_description = '$meta_description', meta_keywords = '$meta_keywords', target='$target', category='$category', menu_image='$menu_image', custom_data='$custom_data', theme='$theme' WHERE id=$id");
    
     add_hook("after_edit_page");
    
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
         and $acl -> hasPermission("logo")){
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
        
         add_hook("before_upload_logo");
         move_uploaded_file($logo_upload['tmp_name'], $new_filename);
         $image_size = getimagesize($new_filename);
         if($image_size[0] <= 500 and $image_size[1] <= 100){
             setconfig("logo_image", $logo_upload_filename);
            
             add_hook("after_upload_logo_successfull");
             }else{
             header("Location: index.php?action=logo_upload&error=to_big");
            
             add_hook("after_upload_logo_failed");
             exit();
             }
        
         }
    
     add_hook("after_upload_logo");
    
    
    
     }



if(($_POST["edit_admin"] == "edit_admin" && $acl -> hasPermission("users"))
         or ($_POST["edit_admin"] == "edit_admin" and logged_in()
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
     $username = db_escape($_POST["admin_username"]);
     $lastname = db_escape($_POST["admin_lastname"]);
     $firstname = db_escape($_POST["admin_firstname"]);
     $email = db_escape($_POST["admin_email"]);
     $password = $_POST["admin_password"];
     $rechte = db_escape($_POST["admin_rechte"]);
    
     $notify_on_login = intval(isset($_POST["notify_on_login"]));
    
     if(isset($_POST["group_id"])){
         $group_id = $_POST["group_id"];
         if($group_id == "-")
             $group_id = "NULL";
         else
             $group_id = intval($group_id);
        
         }else{
         $group_id = $_SESSION["group_id"];
         }
    
     $icq_id = db_escape($_POST["icq_id"]);
     $skype_id = db_escape($_POST["skype_id"]);
     $about_me = db_escape($_POST["about_me"]);
     $html_editor = db_escape($_POST["html_editor"]);
    
     add_hook("before_edit_user");
     db_query("UPDATE " . tbname("users") . " SET username = '$username', `group`= $rechte, `group_id` = " . $group_id . ", firstname='$firstname',
lastname='$lastname', notify_on_login='$notify_on_login', email='$email', 
`icq_id`='$icq_id', skype_id = '$skype_id',
about_me = '$about_me', avatar_file = '$db_avatar_filename', html_editor='$html_editor' WHERE id=$id");
    
     if(!empty($password))
         changePassword($password, $id);
    
     add_hook("after_edit_user");;
     if(!$acl -> hasPermission("users")){
         header("Location: index.php");
         exit();
         }else{
         header("Location: index.php?action=admins");
         exit();
         }
    
     }

if($_POST["edit_banner"] == "edit_banner" && $acl -> hasPermission("banners")){
     $name = db_escape($_POST["banner_name"]);
     $image_url = db_escape($_POST["image_url"]);
     $link_url = db_escape($_POST["link_url"]);
     $category = intval($_POST["category"]);
     $id = intval($_POST["id"]);
    
     $type = db_escape($_POST["type"]);
     $html = db_escape($_POST["html"]);
     $language = db_escape($_POST["language"]);
     add_hook("before_edit_banner");
     $query = db_query("UPDATE " . tbname("banner") . " 
SET name='$name', link_url='$link_url', image_url='$image_url', category='$category', type='$type', html='$html', language='$language' WHERE id=$id");
    
     add_hook("after_edit_banner");
     header("Location: index.php?action=banner");
     exit();
    
     }

if($_POST["edit_key"] == "edit_key" && $acl -> hasPermission("expert_settings")){
     $name = db_escape($_POST["name"]);
     $value = db_escape($_POST["value"]);
     $id = intval($_POST["id"]);
     add_hook("before_edit_key");
     $query = db_query("UPDATE " . tbname("settings") . " 
SET name='$name',value='$value' WHERE id=$id");
    
     add_hook("after_edit_key");
    
     header("Location: index.php?action=settings");
     exit();
    
     }
