<?php
define("PACKAGE_SOURCE_BASE_PATH", "D:\\Server Sicherung\\2015-11-25\\dateien\\ulicms2015\\packages");
define("PACKAGE_SOURCE_BAS_URL", "http://packages.ulicms.de/");

function isNotExcluded($file){
   return($file != "." and $file != ".." and $file != "logs" and $file != "usage");
}

function package_source_version_list(){
	   $html = "<ul>";
	   $files = scandir(PACKAGE_SOURCE_BASE_PATH. "/");
	   natcasesort($files);
	   for($i=0; $i < count ($files); $i++){
	       $file = $files[$i];
	       $fullpath = PACKAGE_SOURCE_BASE_PATH. "/".$file;
		   if(is_dir($fullpath) and isNotExcluded($file)){
			  $html .= "<li>";
			  $html .= '<a href="'.buildSEOUrl()."?ulicms_version=".basename($file). '">'.htmlspecialchars($file)."</a>";
			  $html .= "</li>";
		   }
	   }
	$html .= "</ul>";
	return $html;
}

function package_source_package_list(){
	    $html = "<ol>";
	   $packages = file_get_contents(PACKAGE_SOURCE_BASE_PATH."/". basename($_GET["ulicms_version"])."/list.txt");
	   $packages = explode("\r\n", $packages);
	   foreach($packages as $package){
	          if(!empty($package)){
				  $html .= "<li>";
				  $html .= '<a href="'.buildSEOUrl()."?ulicms_version=".htmlspecialchars($_GET["ulicms_version"]). "&package = ".htmlspecialchars($package).
				  '">'.htmlspecialchars($package)."</a>";
				  $html .= "</li>";
			  }
		   }
	   
	$html .= "</ol>";
	return $html;
}

function package_source_render(){
   $html = "";
   if(empty($_GET["ulicms_version"])){
      $html = package_source_version_list();
   } else{
      if(empty($_GET["package"])){
	     $html = package_source_package_list();
	  }
   }
   return $html;
}