<?php 
function blog_list(){


   $posts_per_page = 5;
   
   $html = "";
   
   // Wenn der Nutzer mindestens die Berechtigungen
   // eines Mitarbeiters hat, bekommt er den Link zum 
   // Anlegen eines neuen Blogbeitrag angezeigt
   
   if($_SESSION["group"] >= 20){
   $html .= "<p><a href='?seite=".
   get_requested_pagename().
   "&blog_admin=add'>Blogeintrag anlegen</a><hr/></p>";
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
    $_SESSION["language"]."'");
    $total_entries = mysql_num_rows($count_query);

    $query = mysql_query("SELECT * FROM `".tbname("blog")."` WHERE language='".$_SESSION["language"]."' ORDER by id DESC LIMIT $limit1, $limit2");


    $html = "";
    
    if(mysql_num_rows($query) > 0){
          while($post = mysql_fetch_object($query)){
          $user = getUserById($post->author);    
          
          
          
          $html.= "<h2 class='blog_headline'><a href='?seite=".get_requested_pagename()."&amp;single=".$post->seo_shortname."'>".$post->title."</a></h2>";
          $html.= "<hr class='blog_hr'/>";
          $html.= "<sub><strong>".
          date(getconfig("date_format"), $post->datum)." - Autor: ". $user["username"].
          "</strong></sub><br/><br/>";
          $html.= "<div class='blog_post_content'>".$post->content_preview."</div>";
       }


   $html.= "<br/><div class='page_older_newer'>";
   
   
    if($limit1 > 0){
                     
                   
          
   $html.= "<a href='?seite=".get_requested_pagename()."&amp;limit=".($limit3)."'>";
   } 
   
   if($_SESSION["language"] == "de"){
      $html .= "Neuer";
   
   }else{
     $html .= "newer";
     
   }   
   
   if($limit3 > 0){
       $html.= "</a>";
   }
   
   $html .= "&nbsp;&nbsp;";
   
   
  
      $html.= "<a href='?seite=".get_requested_pagename()."&amp;limit=".$limit2."'>";
   
   
   if($_SESSION["language"] == "de"){
      $html .= "Ältere";
   
   }else{
     $html .= "older";
     
   }   
   
  
   
     $html.= "</a>";
  
   
   
   
   $html.= "</div>";

   return $html;

}else{
$html = "<p class='ulicms_error'>";

if($_SESSION["language"] == "de"){
   $html .= "Es sind keine weiteren Blogeinträge vorhanden";
   
}else{

   $html .= "There are no other blog-entries available!";
}


$html.= "</p>";

return $html;

}



}
?>