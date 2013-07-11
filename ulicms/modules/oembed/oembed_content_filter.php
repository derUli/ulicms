<?php 
function oembed_content_filter($content){
   include_once getModulePath("oembed")."class_oembed.php";
   $oembed = new OEmbed(); 
   $content = $oembed->getHTML($content, array('width' => 300));
   return $content;

}
?>