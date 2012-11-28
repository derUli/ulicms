<?php 

function blog_single($seo_shortname){
    
    $query = mysql_query("SELECT * FROM `".tbname("blog")."` WHERE seo_shortname='$seo_shortname'");



    if(mysql_num_rows($query) > 0){
       $post = mysql_fetch_object($query);
       $user = getUserById($post->author);    
      
       $html = "";
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
	   
       return $html;
    }else{

       return "<p class='ulicms_error'>Dieser Blogartikel existiert nicht mehr.<br/>
       Vielleicht bist du einem toten Link gefolgt?</p>";


    }

}
?>