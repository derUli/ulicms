<?php 

function blog_single($seo_shortname){
    
    $query = mysql_query("SELECT * FROM `".tbname("blog")."` WHERE seo_shortname='$seo_shortname'");

    if(mysql_num_rows($query) > 0){
       $post = mysql_fetch_object($query);
       $html = "";
       $html.= "<h2 class='blog_headline'>".$post->title."</h2>";
       $html.= "<hr class='blog_hr'/>";
       $html.= "<sub>".
       date(getconfig("date_format"), $post->datum).
       "</sub><br/><br/>";
       $html.= "<div class='blog_post_content'>".$post->content_full."</div>";

       return $html;
    }else{

       return "<p class='ulicms_error'>Dieser Blogartikel existiert nicht mehr.<br/>
       Vielleicht bist du einem toten Link gefolgt?</p>";


    }

}
?>