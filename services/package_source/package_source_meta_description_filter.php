<?php
function package_source_meta_description_filter($meta_description){
   if(!empty($_GET["ulicms_version"]) and !empty($_GET["package"]) and containsModule(null, "package_source")){
   $text = @file_get_contents(PACKAGE_SOURCE_BASE_PATH."/". basename($_GET["ulicms_version"])."/descriptions/".
      basename($_GET["package"].".txt"));
   if($text){
      $meta_description = strip_tags($text);
   }
	  }
	  
	return $meta_description;
   }
