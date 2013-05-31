<?php 
function blog_meta_keywords_filter($txt){
   include "lib/string_functions.php";
   $single = mysql_real_escape_string($_GET["single"]);
   $query = mysql_query("SELECT content_full FROM `".tbname("blog")."` WHERE seo_shortname='$single'");
   $content_full = false; 
  
   if(mysql_num_rows($query) > 0){
      $result = mysql_fetch_assoc($query);
      $content_full = $result["content_full"];
   }
 
     

   if(!containsModule(get_requested_pagename(), "blog") or !$single or !$content_full)
      return $txt;

   include_once "lib/string_functions.php";
   $maxlength_chars = 160;
   $content_full = strip_tags($content_full);


   // $shortstring = preg_replace('/(?:[ \t]*(?:\n|\r\n?)){2,}/', "\n", $shortstring);
   
   // Leerzeichen und Zeilenumbrüche entfernen
   $content_full = trim($content_full);
   $content_full = preg_replace("#[ ]*[\r\n\v]+#", "\r\n", $content_full); 
   $content_full = preg_replace("#[ \t]+#", " ", $content_full);
   $content_full = str_replace("\r\n", " ", $content_full);
   $content_full = str_replace("\n", " ", $content_full);
   $content_full = str_replace("\r", " ", $content_full);
   $content_full = str_replace("&nsbp;", " ", $content_full);
   
   $content_full = trim($content_full);
   
   $stripped_content = trim($content_full);
   $stripped_content = str_replace("\\r\\n", "\r\n", $stripped_content);
   $stripped_content = strip_tags($stripped_content);
   $words = keywordsFromString($stripped_content);
   $maxWords = 10;
   $currentWordCount = 0;
   $maxi = count($words);
   $i = 0;
   $meta_keywords = Array();
   if(count($words) > 0){
   foreach ($words as $key => $value) {
     $i++;
     $key = trim($key);

     if(!empty($key) and $currentWordCount <= $maxWords){
        $currentWordCount++;
        array_push($meta_keywords, $key);
        
     }   
     
     }
 
	      
	      
   }
	
   $meta_keywords = implode(", ", $meta_keywords);

   $meta_keywords = mysql_real_escape_string($meta_keywords);



   return $meta_keywords;
   
}
?>