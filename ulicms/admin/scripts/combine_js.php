<?php
  $compressed_file = dirname(__file__)."/compressed.js";

if (php_sapi_name () == "cli"){
  $output = "";
  array_shift ($argv);
  if(in_array("rebuild", $argv)){
     $dir = dirname(__file__);
     $files = scandir($dir);
     var_dump($files);
     foreach($files as $file){
        $filepath = $dir."/".$file;
        $file_parts = pathinfo($file);
        
        if(!is_dir($filepath)){
          $extension = $file_parts["extension"];
           if($extension == "js"){
           $output .= "// File " . $file."\r\n";
           $output .= file_get_contents($filepath);
           $output .= "\r\n";
           }
           
           
           }
           
 
     
     
     }
     
         $output = str_replace("\r\n", "\n", $output);
         $output = str_replace("\r", "\n", $output);
         $output = str_replace("\n", "\r\n", $output);
         file_put_contents($compressed_file, $output);
  }
}