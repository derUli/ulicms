<?php

function blog_render(){

     // Prüfen, ob Blog schon installiert
     blog_check_installation();


    if(!empty($_GET["single"])){
       require_once getModulePath("blog")."blog_single.php";
       return blog_single(mysql_real_escape_string($_GET["single"]));
    }
    else if(!empty($_GET["blog_admin"])){
        if($_GET["blog_admin"] == "add"){
           require_once getModulePath("blog")."blog_add.php";  
           return blog_add_form(); 
        }
		else if($_GET["blog_admin"] == "edit_post"){
           require_once getModulePath("blog")."blog_edit.php";  
           return blog_edit_form(intval($_GET["id"])); 
        }
        else if($_GET["blog_admin"] == "submit"){
           return blog_submit();  
                 
        }
		else if($_GET["blog_admin"] == "update"){
			return blog_update();
		}
		else if($_GET["blog_admin"] == "delete_post"){
		   require_once getModulePath("blog")."blog_remove.php";  
		   returnblog_remove_post(intval($_GET["id"]));
		}
    } 
	
  
    else{
       require_once getModulePath("blog")."blog_list.php";
       return blog_list();
    }

}




function blog_update(){

  $html_output = "";

  $title = mysql_real_escape_string($_POST["title"]);
  $seo_shortname = mysql_real_escape_string($_POST["seo_shortname"]);

  if(empty($title) or empty($seo_shortname)){
      $html_output .= "<script type='text/javascript'>
     history.back()     
     </script>";
     return $html_output;
	 
	 

  }
  
  $language = mysql_real_escape_string($_POST["language"]);
  $comments_enabled = mysql_real_escape_string($_POST["comments_enabled"]);
  $entry_enabled = mysql_real_escape_string($_POST["entry_enabled"]);
  
  $content_full = mysql_real_escape_string($_POST["content_full"]);
  $content_preview = mysql_real_escape_string($_POST["content_preview"]);
  $date = time();
  $author = $_SESSION["login_id"];
  
  $id = intval($_POST["id"]);
 
  // Rechte prüfen
  if($_SESSION["group"] >= 20)  {
     $insert_query = "UPDATE `".tbname("blog")."` SET title = '$title',
	 seo_shortname = '$seo_shortname', comments_enabled = $comments_enabled,
	 entry_enabled = $entry_enabled, language = '$language', content_full = '$content_full',
	 content_preview = '$content_preview' WHERE id = $id
	 ";
  $html_output .= $insert_query;
  mysql_query($insert_query);
  $html_output .= "<script type='text/javascript'>
  location.replace('?seite=".get_requested_pagename().
  "&single=".$seo_shortname. "');
  </script>
  ";
  
  
  }
  

return $html_output;
}



function blog_submit(){

  $html_output = "";

  $title = mysql_real_escape_string($_POST["title"]);
  $seo_shortname = mysql_real_escape_string($_POST["seo_shortname"]);

  if(empty($title) or empty($seo_shortname)){
     $html_output .= "<script type='text/javascript'>
     history.back()     
     </script>";
     return $html_output;


  }
  
  $language = mysql_real_escape_string($_POST["language"]);
  $comments_enabled = mysql_real_escape_string($_POST["comments_enabled"]);
  $entry_enabled = mysql_real_escape_string($_POST["entry_enabled"]);
  
  $content_full = mysql_real_escape_string($_POST["content_full"]);
  $content_preview = mysql_real_escape_string($_POST["content_preview"]);
  $date = time();
  $author = $_SESSION["login_id"];
  
  // Rechte prüfen
  if($_SESSION["group"] >= 20)  {
     $insert_query = "INSERT INTO `".tbname("blog")."` (datum, ".
     "title, seo_shortname, comments_enabled, language, 
  entry_enabled, author, 
  content_full, content_preview) VALUES ($date, '$title', 
  '$seo_shortname', $comments_enabled, '$language', $entry_enabled,
  $author, '$content_full', '$content_preview')";
  $html_output .= $insert_query;
  mysql_query($insert_query);
  $html_output .= "<script type='text/javascript'>
  location.replace('?seite=".get_requested_pagename().
  "&single=".$seo_shortname. "');
  </script>
  ";
  
  }
  

return $html_output;
}



function blog_check_installation(){
	$test = mysql_query("SELECT * FROM ".tbname("blog"));
	if(!$test){
  	require_once getModulePath("blog")."blog_install.php";
  	blog_do_install();		
	}	
}







?>