<?php
include_once "init.php";
$all_files = find_all_files(ULICMS_ROOT);
  
   $less_files = array();
foreach($all_files as $file){
   if(endsWith($file, ".less")){
       $less_files[] = $file;
   }
}

var_dump($less_files);
foreach($less_files as $file){
   $path_parts = pathinfo($file);
   $new_file = $path_parts['dirname']."/".$path_parts['filename'].".css";
   var_dump($new_file);
}