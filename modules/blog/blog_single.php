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


function blog_display_comments($post_id){
    $html = "";
    $query = mysql_query("SELECT * FROM `".tbname("blog_comments")."` WHERE post_id = $post_id");
    
    $html .= "<div class='comments'>";
    $html .= "<h2>Kommentare</h2>";
    
    if(mysql_num_rows($query) > 0){
        
	$html.="<p>Es sind bisher ".mysql_num_rows($query).
	"zu diesem Artikel vorhanden.</p>";
    
	while($comment = mysql_fetch_object($query)){
	   $html.="<div class='a_comment'>
	   <a name='comment".$comment->id."'></a>Kommentar Nr. ".$comment->id."</div>";
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