<?php
function blog_title_filter($txt){
     $single = mysql_real_escape_string($_GET["single"]);
     $query = db_query("SELECT * FROM `" . tbname("blog") . "` WHERE seo_shortname='$single'");
     $title = false;
    
     if(!$query)
         return $txt;
    
     if(mysql_num_rows($query) > 0){
         $result = mysql_fetch_assoc($query);
         $title = $result["title"];
         }
    
     if(!containsModule(get_requested_pagename(), "blog") or !$single or !$title)
         return $txt;
    
     return $title;
    
    }
?>