<?php
function blog_seo_meta_description_filter($txt){
     $single = mysql_real_escape_string($_GET["single"]);
     $query = db_query("SELECT content_preview FROM `" . tbname("blog") . "` WHERE seo_shortname='$single'");
     $content_preview = false;
    
     if(!$query)
         return $txt;
    
     if(mysql_num_rows($query) > 0){
         $result = mysql_fetch_assoc($query);
         $content_preview = $result["content_preview"];
         }
    
     if(!containsModule(get_requested_pagename(), "blog") or !$single or !$content_preview)
         return $txt;
    
     include_once "lib/string_functions.php";
     $maxlength_chars = 160;
     $content_preview = strip_tags($content_preview);
    
    
     // $shortstring = preg_replace('/(?:[ \t]*(?:\n|\r\n?)){2,}/', "\n", $shortstring);
    // Leerzeichen und Zeilenumbrüche entfernen
    $content_preview = trim($content_preview);
     $content_preview = preg_replace("#[ ]*[\r\n\v]+#", "\r\n", $content_preview);
     $content_preview = preg_replace("#[ \t]+#", " ", $content_preview);
     $content_preview = str_replace("\r\n", " ", $content_preview);
     $content_preview = str_replace("\n", " ", $content_preview);
     $content_preview = str_replace("\r", " ", $content_preview);
     $content_preview = str_replace("&nbsp;", " ", $content_preview);
    
     $content_preview = trim($content_preview);
    
     $shortstring = $content_preview;
    
     $word_count = str_word_count($shortstring);
    
     while(strlen($shortstring) > $maxlength_chars){
         $shortstring = getExcerpt($content_preview, 0, $word_count);
         $word_count -= 1;
         }
    
     return $shortstring;
    
     }
?>