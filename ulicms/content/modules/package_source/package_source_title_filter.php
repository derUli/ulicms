<?php
function package_source_title_filter($title){
if(empty($_GET["ulicms_version"])){
      $title = get_translation("SELECT_ULICMS_VERSION");
   } else{
      if(empty($_GET["package"])){
	     $title = get_translation("ALL_PACKAGES_FOR", array
		 ("%version%" => htmlspecialchars($_GET["ulicms_version"])));
	  } else {
	    
   $text = get_translation("PACKAGE_FOR_VERSION", 
   array(
   "%paket%" => htmlspecialchars($_GET["package"]), 
   "%version%" => htmlspecialchars($_GET["ulicms_version"])));
	  }
   }
return $title;
}

