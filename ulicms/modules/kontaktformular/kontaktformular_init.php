<?php
if(!function_exists("stringcontainsbadwords")){
     function stringcontainsbadwords($str){
         $words_blacklist = getconfig("spamfilter_words_blacklist");
         $str = strtolower($str);
        
         if($words_blacklist !== false){
             $words_blacklist = explode("||", $words_blacklist);
             }
        else{
             return false;
             }
        
         for($i = 0; $i < count($words_blacklist); $i++){
             $word = strtolower($words_blacklist[$i]);
             if(strpos($str, $word) !== false)
                 return true;
             }
        
        
         return false;
         }
    
     }
