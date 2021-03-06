<?php
require_once 'init.php';
require_once ULICMS_ROOT."/classes/objects/path.php";

$contentFolder = Path::resolve("ULICMS_ROOT/content");
$files = find_all_files($contentFolder);
$editableFileTypes = array("php", "css", "html", "js", "json");
$editableFiles = [];
foreach($files as $file){
   $ext = file_extension($file);
   if(in_array($ext, $editableFileTypes) and is_file($file)){
	   $file = substr ($file, strlen(ULICMS_ROOT));
	   $editableFiles[] = $file;
	   
   }
}

natcasesort($editableFiles);

var_dump($editableFiles);