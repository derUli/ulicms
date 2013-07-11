<?php 
function oembed_content_filter($content){
   include_once getModulePath("oembed")."class_oembed.php";
   $oembed = new OEmbed(); 
   
   $args = array();   
   
   $oembed_width = getconfig($oembed_width);
   if($oembed_width )
      $args["width"] = $oembed_width;
      
   
   $oembed_height = getconfig($oembed_height);
   if($oembed_height )
      $args["height"] = $oembed_height;
   
   preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $content, $links);
   for($t=0; $t < count($links); $t++){
        $html = $oembed->getHTML($links[$t][0], $args);

        $content = str_replace($links[$t][0], $html, $content);
   }
   
   return $content;

}
?>