<?php
function delete_page($id = false, $systemname = false){
     if($id){
         mysql_query("DELETE FROM " . tbname("content") . " WHERE id=$id");
         return mysql_affected_rows() > 0;
         }
    
     if($systemname){
         mysql_query("DELETE FROM " . tbname("content") . " WHERE systemname='$systemname'");
         return mysql_affected_rows() > 0;
         }
    
     return false;
    }

function add_page($system_title, $page_title, $page_content, $position, $activated = 1,
     $comments_enabled = 0, $redirection = "", $menu = "top",
     $parent = "NULL", $language = "de", $access = array("all"),
     $target = "_self", $meta_keywords = "", $meta_description = ""){
     $system_title = mysql_real_escape_string($system_title);
     $page_title = mysql_real_escape_string($page_title);
     $page_content = $page_content;
     $notinfeed = 0;
     $redirection = mysql_real_escape_string($redirection);
     $menu = mysql_real_escape_string($menu);
     $position = $position;
    
     if($parent == "NULL")
         $parent = "NULL";
     else
         $parent = mysql_real_escape_string($parent);
    
     $access = implode(",", $access);
     $access = mysql_real_escape_string($access);
     $target = mysql_real_escape_string($target);
    
     $page_content = mysql_real_escape_String($page_content);
     $language = mysql_real_escape_string($language);
    
     $meta_keywords = mysql_real_escape_String($meta_keywords);
     $meta_description = mysql_real_escape_String($meta_description);
    
     if(!isset($_SESSION["login_id"])){
         $session_id = 1;
         }else{
         $session_id = $_SESSION["login_id"];
         }
    
     return db_query("INSERT INTO " . tbname("content") .
         " (systemname,title,content,parent, active,created,lastmodified,autor,
  comments_enabled,notinfeed,redirection,menu,position, 
  access, meta_description, meta_keywords, language, target) 
  VALUES('$system_title','$page_title','$page_content',$parent, $activated," . time() . ", " . time() .
         "," . $session_id .
         ", " . $comments_enabled .
         ",$notinfeed, '$redirection', '$menu', $position, '" . $access . "', 
  '$meta_description', '$meta_keywords',
  '$language', '$target')") !== false;
    
    }

?>