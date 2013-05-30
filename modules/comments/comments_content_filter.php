<?php 
function comments_content_filter($content){
   return str_replace("UliCMS", "deiner Mutter", $content);
}
?>