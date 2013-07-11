<?php 
function oembed_content_filter($content){
   include_once getModulePath("oembed")."class_oembed.php";
   $oembed = new OEmbed(); 
   preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $content, $links);
   for($t=0; $t < count($links); $t++){
        $html = $oembed->getHTML($links[$t][0], array('width' => 300));
        $content = str_replace($links[$i][0], $html, $content);
   }
   
   return $content;

}
?>