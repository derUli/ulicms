<?php
function resetScriptQueue(){
   $_SERVER["script_queue"] = array();
}

function enqueueScriptFile($path){
   if(!isset($_SERVER["script_queue"])){
         $_SERVER["script_queue"] = array();
   }
   $_SERVER["script_queue"][] = $path;
   
}

function getCombinedScripts(){
   $output = "";
   if(isset($_GET["output_scripts"])){
      $scripts = explode(";", $_GET["output_scripts"]);
      foreach($scripts as $script){
          if(is_file($script)){
             $ext = pathinfo($script, PATHINFO_EXTENSION);
             if($ext == "js"){
                $content = @file_get_contents($script);
 
                if($content){
                   $content = str_replace("\r\n", "\n", $content);
                   $content = str_replace("\r", "\n", $content);
                   $content = str_replace("\n", "\r\n", $content);
                   $content = trim($content);
                   $output .= "// Script ".$script."\r\n";
                   $output .= $content;
                   $output .= "\r\n";
                   $output .= "\r\n";
                }
             }
             
          }
      }
   }
   header("Content-Type: text/javascript");
   $len = mb_strlen($content , 'binary');
   
   echo $output;
   exit();
}

function combined_script_html(){
  echo get_combined_script_html();
}

function get_combined_script_html(){
   $html = '<script src="'.getCombinedScriptURL().'" type="text/javascript"></script>';
   return $html;
}

function getCombinedScriptURL(){
  $output = "";
  if(isset($_SERVER["script_queue"]) and is_array($_SERVER["script_queue"])){
  $files = implode(";", $_SERVER["script_queue"]);
     $url = "?output_scripts=".$files;
  } else {
     $url="index.php?scripts=";
  }
   
   return $url;
}