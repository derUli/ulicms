<?php 
function oembed_content_filter($content){
   include_once getModulePath("oembed")."class_oembed.php";
   $oembed = new OEmbed(); 
   
   $args = array();   
   
   $oembed_width = getconfig("oembed_width");
   if($oembed_width )
      $args["width"] = $oembed_width;
      
   
   $oembed_height = getconfig("oembed_height");
   if($oembed_height )
      $args["height"] = $oembed_height;
      
   $oembed_maxwidth = getconfig("oembed_maxwidth");
   
   if($oembed_maxwidth )
      $args["maxwidth"] = $oembed_maxwidth;
      
   
   $oembed_maxheight = getconfig("oembed_maxheight");
   if($oembed_maxheight )
      $args["maxheight"] = $oembed_maxheight;
   
   preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', strip_tags($content), $links);
   
   for($t=0; $t < count($links); $t++){
        $html = $oembed->getHTML($links[0][$t], $args);
        $content = str_ireplace($links[0][$t], $html, $content);
   }
   
   return $content;

}
?>
