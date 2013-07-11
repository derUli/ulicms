<?php 
function oembed_content_filter($content){
   include_once getModulePath("oembed")."class_oembed.php";
   $oembed = new OEmbed(); 
   
   $oembed_width = getconfig("oembed_width");
   if(!$oembed_width )
      $oembed_width = 400;
      
   
   $oembed_height = getconfig("oembed_height");
   if(!$oembed_height )
      $oembed_height = 900;
   
   preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $content, $links);
   for($t=0; $t < count($links); $t++){
        $html = $oembed->getHTML($links[$t][0], array('width' => $oembed_width, 'height' => $oembed_height));

        $content = str_replace($links[$t][0], $html, $content);
   }
   
   return $content;

}
?>