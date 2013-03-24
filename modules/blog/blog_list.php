<?php 
function blog_list(){


   $posts_per_page = getconfig("blog_posts_per_page");
   
   if($posts_per_page === false){
      setconfig("blog_posts_per_page", "5");
      $posts_per_page = 5;
   }
   
   $autor_and_date = getconfig("blog_autor_and_date_text");
   
   if($autor_and_date === false){
      setconfig("blog_autor_and_date_text", "<sub><strong>".
      "%date% - Autor: %username%".
      "</strong></sub>");
      $autor_and_date = getconfig("blog_autor_and_date_text");
   }
   

   
   
   $html = "";
   
   // Wenn der Nutzer mindestens die Berechtigungen
   // eines Mitarbeiters hat, bekommt er den Link zum 
   // Anlegen eines neuen Blogbeitrag angezeigt
   
   if($_SESSION["group"] >= 20){
      $html .= "<p><a href='?seite=".
      get_requested_pagename().
      "&blog_admin=add'>Blogeintrag anlegen</a></p><br/>";
   }




    if(isset($_GET["limit"])){
      $limit1 = intval($_GET["limit"]);
      $limit2 = intval($_GET["limit"]) + $posts_per_page;
      $limit3 = $limit1 - $posts_per_page;
   
      
    }else{
      $limit1 = 0;
      $limit2 = $posts_per_page;
      $limit3 = $limit1 - $posts_per_page;
      
    }

    $count_query = mysql_query("SELECT * FROM `".
    tbname("blog")."` WHERE language='".
    $_SESSION["language"]."' ORDER by id ASC");
    $first_post = mysql_fetch_object($count_query);
    $oldest_post_id = $first_post->id;
    $total_entries = mysql_num_rows($count_query);


    if($_SESSION["group"] >= 20){
       $query = mysql_query("SELECT * FROM `".tbname("blog")."` WHERE language='".$_SESSION["language"]."' ORDER by datum DESC LIMIT $limit1, ".($posts_per_page - 1));
    }
    else{
       $query = mysql_query("SELECT * FROM `".tbname("blog")."` WHERE language='".$_SESSION["language"]."' AND entry_enabled = 1 ORDER by datum DESC LIMIT $limit1, ".($posts_per_page - 1));
    }


    $html.= "";
    
    if(mysql_num_rows($query) > 0){
          while($post = mysql_fetch_object($query)){
          $user = getUserById($post->author);    
          
          
          
          $html.= "<h2 class='blog_headline'><a href='?seite=".get_requested_pagename()."&amp;single=".$post->seo_shortname."'>".$post->title."</a></h2>";
          $html.= "<hr class='blog_hr'/>";
          
          $date_and_autor_string = $autor_and_date;
          
          $date_and_autor_string = str_replace("%date%",
          date(getconfig("date_format"), $post->datum),
          $date_and_autor_string);
          
          $date_and_autor_string = str_replace("%username%",
          $user["username"],
          $date_and_autor_string);
        
          $date_and_autor_string = str_replace("%firstname%",
          $user["firstname"],
          $date_and_autor_string);
          $date_and_autor_string = str_replace("%lastname%",
          $user["lastname"],
          $date_and_autor_string);
          
          $html .= $date_and_autor_string.
          "<br/><br/>";
          $html.= "<div class='blog_post_content'>".$post->content_preview."</div>";
		  
		  if($_SESSION["language"] == "de"){
		     $html .= "<br/><a href='?seite=".get_requested_pagename()."&amp;single=".$post->seo_shortname."'>weiterlesen...</a>
			 ";
          }else{
		    $html .= "<br/><a href='?seite=".get_requested_pagename()."&amp;single=".$post->seo_shortname."'>read more...</a>
			";
		  }
		  $html.= "<br/><br/>";
		  
		 if(($_SESSION["group"] >= 20 and $_SESSION["login_id"] == $post->author)
		  or ($_SESSION["group"] >= 40)){
		   $html .= "<a href='?seite=".get_requested_pagename()."&blog_admin=edit_post&id=".$post->id."'>[Bearbeiten]</a> ";
		 
           $html .= "<a href='?seite=".get_requested_pagename()."&blog_admin=delete_post&id=".$post->id."' onclick='return confirm(\"Diesen Post wirklich löschen?\")'>[Löschen]</a>";
           
           $html .= "<br/><br/>";
           
		  }else if($_SESSION["group"] >= 20){
		   $html .= "
		   <div class='disabled_link'>[Bearbeiten]</div>
		   <div class='disabled_link'>[Löschen]</div>";
		
           $html .= "<br/><br/>";
		  }
		 
          
          $last_post_id = $post->id;
       }
$html.= "<br/>";

   $html.= "<br/><div class='page_older_newer'>";
   
   
    if($limit3 > -1){
                     
                   
          
   $html.= "<a href='?seite=".get_requested_pagename()."&amp;limit=".($limit3)."'>";
   } 
   
   if($_SESSION["language"] == "de"){
      $html .= "Neuer";
   
   }else{
     $html .= "newer";
     
   }   
   
   if($limit3 > -1){
       $html.= "</a>";
   }
   
   $html .= "&nbsp;&nbsp;";
   
   
   
  if($last_post_id != $oldest_post_id){
      $html.= "<a href='?seite=".get_requested_pagename()."&amp;limit=".$limit2."'>";
   }
   
   if($_SESSION["language"] == "de"){
      $html .= "Ältere";
   
   }else{
     $html .= "older";
     
   }   
   
  
   if($last_post_id != $oldest_post_id){
     $html.= "</a>";
   }
  
   
   
   
   $html.= "</div>";

   return $html;

}else{
$html .= "<p class='ulicms_error'>";

if($_SESSION["language"] == "de"){
   $html .= "Es sind keine weiteren Blogeinträge vorhanden.";
   
}else{

   $html .= "There are no other blog-entries available!";
}


$html.= "</p>";

return $html;

}



}
?>
