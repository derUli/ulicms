<?php 

function blog_single($seo_shortname){
    
    $query = mysql_query("SELECT * FROM `".tbname("blog")."` WHERE seo_shortname='$seo_shortname'");



    if(mysql_num_rows($query) > 0){
       $post = mysql_fetch_object($query);
       $user = getUserById($post->author);    
       
       $html = "";
       
       if($_SESSION["group"] >= 20 or $post->entry_enabled){
       
       $html.= "<h2 class='blog_headline'>".$post->title."</h2>";
       $html.= "<hr class='blog_hr'/>";
       $html.= "<sub><strong>".
       date(getconfig("date_format"), $post->datum)." - Autor: ". $user["username"].
       "</strong></sub><br/><br/>";
       $html.= "<div class='blog_post_content'>".$post->content_full."</div>";

	   $html .= "<br/>";
	   
	    if(($_SESSION["group"] >= 20 and $_SESSION["login_id"] == $post->author)
		  or ($_SESSION["group"] >= 40)){
		   $html .= "<a href='?seite=".get_requested_pagename()."&blog_admin=edit_post&id=".$post->id."'>[Bearbeiten]</a> ";
		 
           $html .= "<a href='?seite=".get_requested_pagename()."&blog_admin=delete_post&id=".$post->id."' onclick='return confirm(\"Diesen Post wirklich löschen?\")'>[Löschen]</a>";
		  }else if($_SESSION["group"] >= 20){
		   $html .= "
		   <div class='disabled_link'>[Bearbeiten]</div>
		   <div class='disabled_link'>[Löschen]</div>";
		  }
	   
       
       
       if($post->comments_enabled){
         $html .= "<br/><br/>".
         blog_display_comments($post->id);       
       }
	   
	   
       return $html;
       
       
       
    }else{
    return "<p class='ulicms_error'>Dieser Blogartikel ist momentan deaktiviert.</p>";
    }
    }else{

       return "<p class='ulicms_error'>Dieser Blogartikel existiert nicht mehr.<br/>
       Vielleicht bist du einem toten Link gefolgt?</p>";


    }

}

function comment_form($post_id){
     $html = "";
     $html .= "<form action=".$_SERVER['REQUEST_URI']."' method='post'>";
     if($_SESSION["language"] == "de"){
        $submit = "Kommentar veröffentlichen";
     }else{
        $submit = "Submit Comment";
     }
     

     $html .= "<table border=0>
     <tr>
     <td><strong>Name: *</strong>&nbsp;&nbsp;</td><td><input name='name' size=50 maxlength=255 type='text' value=''>
     </td>
     </tr>";
     
     
     $html .= "<tr>
     <td><strong>Homepage:</strong>&nbsp;&nbsp;</td><td><input size=50 maxlength=255 name='url' type='text' value='http://'>
     </td>
     </tr>";
     
       $html .= "<tr>
     <td><strong>Email: *</strong>&nbsp;&nbsp;</td><td><input size=50 maxlength=255 name='email' type='text' value=''>
     </td>
     </tr>";
     
     
     $html .= "</table>";

     $html .= "<br/><textarea name='comment' rows=15 cols=60></textarea>";
     $html .= "<input type='text' name='phone' value='' style='visbility:hidden;'>";
     $html .= "<input type='hidden' name='post_comment_to' value='".$post_id."'>";
     $html .= "<br/><br/><input type='submit' value='".$submit."'>";
     $html .= "</form>";
     
     return $html;
}


function post_comments(){
   if(isset($_POST["post_comment_to"])){
      $name = mysql_real_escape_string(htmlspecialchars($_POST["name"]));
      $url = mysql_real_escape_string(htmlspecialchars($_POST["url"]));
      $email = mysql_real_escape_string(htmlspecialchars(
      $_POST["email"]));
      $date = time();
      $comment = mysql_real_escape_string($_POST["comment"]);
     
      if(!empty($name) and !empty($email) and !empty($comment)){
	mysql_query("INSERT INTO `".tbname("blog_comments").
	"`");
	
	return true;
      } else{
        return false;      
      }
      
      // Spam Protection
      // ein für echte Menschen unsichtbares Textfeld
      // Die meisten Spambots füllen alle Felder aus
      // dieses Feld wird darauf geprüft, ob es nicht leer ist
      if(!empty($_POST["phone"])){
	die("Die motherfucking spammers!");
      }
   }
}

function blog_display_comments($post_id){
    $html = "";
    $query = mysql_query("SELECT * FROM `".tbname("blog_comments")."` WHERE post_id = $post_id");
    
    $html .= "<div class='comments'>";
    if($_SESSION["language"] == "de"){
      $html .= "<h2>Kommentare</h2>";
    }
    else{
      $html .= "<h2>Comments</h2>";
    }
    $html .= comment_form($post_id);
    
    if(mysql_num_rows($query) > 0){
        if($SESSION["language"] == "de"){
	  $html.="<p>Es sind bisher ".mysql_num_rows($query).
	" zu diesem Artikel vorhanden.</p>";
	} else{
	  $html.="<p>There are ".mysql_num_rows($query). " Comments
	 until now.</p>";
	}
	
	$html.="<hr/>";
    
	while($comment = mysql_fetch_object($query)){
	   $html.="<div class='a_comment'>
	   <a href='#comment".$comment->id."' name='comment".$comment->id."'>";
	     $html .= "#".$comment->id;
	     
	 
	     $html .= " ";
	     $html .= $comment->name;
	   
	     $html .= "</a>";
	     
	     if($_SESSION["group"] >= 40){
               $html .= " <a href='?seite=".get_requested_pagename()."&blog_admin=delete_comment&id=".$comment->id."' onclick='return confirm(\"Diesen Kommentar wirklich löschen?\")'>[Löschen]</a>";	     
	     }
	     
	     $html .= "<br/>";
	     $html .= "<br/>";
	       if($_SESSION["language"] == "de"){
	       $html .= "<strong>Datum:</strong>";
	       
	     } else{
               $html .= "<strong>Date:</strong>";
	     }
	     
	     $html.= " ";
	     $html .= date(getconfig("date_format"),
	     $comment->date);
	     $html .= "<br/>";
	     $html .= "<strong>Homepage:</strong> "."<a href='".$comment->url."'>".$comment->url."</a>";
	     $html .= "<br/><br/>";
	     $html .= nl2br(htmlspecialchars($comment->comment));
	     
	     $html .= "<br/><br/>";
	     
	     
	   
	   $html .= "</div>";
	}

   
       
    }else{
	if($_SESSION["language"] == "de"){
	   $html .= "<p>Es sind bisher noch keine Kommentare zu diesem Artikel vorhanden.</p>";
	}
	else{
           $html .= "<p>No Comments existing yet.</p>";
	}
    }
    
    $html .= "</div>";

return $html;
}
?>