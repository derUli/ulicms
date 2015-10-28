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