<?php 
function strip_tags_content_filter($content){

   $allowed_tags = getconfig("allowed_tags");
   return strip_tags($content, $allowed_tags);
}
?>