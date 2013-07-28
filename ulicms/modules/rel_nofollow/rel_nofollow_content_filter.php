<?php
function auto_nofollow_callback($matches){
     $link = $matches[0];
     $http = "http://";
     $https = "https://";
     if (strpos($link, 'rel') === false){
         $link = preg_replace("%(href=\S($http))%i", 'rel="nofollow" $1', $link);
         $link = preg_replace("%(href=\S($https))%i", 'rel="nofollow" $1', $link);
         }elseif (preg_match("%href=\S($site_link)%i", $link)){
         $link = preg_replace('/rel=\S(?!nofollow)\S*/i', 'rel="nofollow"', $link);
         }
     return $link;
    }

function rel_nofollow_content_filter($content){
    
     return preg_replace_callback('/<a[^>]+/', 'auto_nofollow_callback', $content);
    
    }




?>