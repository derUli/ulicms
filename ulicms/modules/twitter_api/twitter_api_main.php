<?php 
function twitter_api_render(){
   if(!class_exists("Twitter")){
      include_once getModulePath("twitter_api")."twitter.class.php";
   }
   return "";
}
?>
