<?php
class html_helper{


 public static function htmlRemoveTagByName($html, $tag){
    
     $doc = new DOMDocument();
     // load the HTML string we want to strip
    $doc -> loadHTML($html);
    
     // get all the script tags
    $script_tags = $doc -> getElementsByTagName($tag);
    
     $length = $script_tags -> length;
    
     // for each tag, remove it from the DOM
    for ($i = 0; $i < $length; $i++){
         $script_tags -> item($i) -> parentNode -> removeChild($script_tags -> item($i));
         }
    
     // get the HTML string back
    $no_script_html_string = $doc -> saveHTML();
    
     return $no_script_html_string;
    
     }
}
