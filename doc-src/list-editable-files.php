<?php
include_once 'init.php';
include_once ULICMS_ROOT."/classes/objects/path.php";

$contentFolder = Path::resolve("ULICMS_ROOT/content");
$files = find_all_files($contentFolder);
$editableFileTypes = array("php", "css", "html", "js", "json");
$editableFiles = array();
foreach($files as $file){
   $ext = file_extension($file);
   if(in_array($ext, $editableFileTypes)){
	   $file = substr ($file, strlen(ULICMS_ROOT));
	   $editableFiles[] = $file;
	   
   }
}

natcasesort($editableFiles);

var_dump($editableFiles);