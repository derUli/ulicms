<?php
// Javascript Minify Funktionen
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
   $lastmod = 0;
   $output = "";
   if(isset($_GET["output_scripts"])){
      $scripts = explode(";", $_GET["output_scripts"]);
      foreach($scripts as $script){
          $script = ltrim($script, "/");
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
                   if(filemtime($script) > $lastmod)
                      $lastmod = filemtime($script); 
                }
             }
             
          }
      }
   }
   
   $output = trim($output);
   
   header("Content-Type: text/javascript");
   $len = mb_strlen($content , 'binary');
   header("Content-Length: ". $len);
   eTagFromString($output);
   browsercacheOneDay($lastmod);
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

// Ab hier Stylesheet Funktionen

function resetStylesheetQueue(){
   $_SERVER["stylesheet_queue"] = array();
}

function enqueueStylesheet($path){
   if(!isset($_SERVER["stylesheet_queue"])){
         $_SERVER["stylesheet_queue"] = array();
   }
   $_SERVER["stylesheet_queue"][] = $path;
   
}

function getCombinedStylesheets(){
   $output = "";
   $lastmod = 0;
   if(isset($_GET["output_stylesheets"])){
      $stylesheets = explode(";", $_GET["output_stylesheets"]);
      
      foreach($stylesheets as $stylesheet){
      $stylesheet = ltrim($stylesheet, "/");
          if(is_file($stylesheet)){
             $ext = pathinfo($stylesheet, PATHINFO_EXTENSION);
             if($ext == "css"){
                $content = @file_get_contents($stylesheet);
 
                if($content){
                   $content = str_replace("\r\n", "\n", $content);
                   $content = str_replace("\r", "\n", $content);
                   $content = str_replace("\n", "\r\n", $content);
                   $content = trim($content);
                   $output .= "/* Stylesheet ".$stylesheet." */\r\n";
                   $output .= $content;
                   $output .= "\r\n";
                   $output .= "\r\n";
                   if(filemtime($script) > $lastmod)
                      $lastmod = filemtime($script); 
                }
             }
             
          }
      }
   }
   
   $output = trim($output);
   header("Content-Type: text/css");
   $len = mb_strlen($content , 'binary');
   header("Content-Length: ". $len);
   eTagFromString($output);
   browsercacheOneDay($lastmod);
   echo $output;
   exit();
}

function combined_stylesheet_html(){
  echo get_combined_stylesheet_html();
}

function get_combined_stylesheet_html(){
   $html = '<link rel="stylesheet" type="text/css" href="'.getCombinedStylesheetURL().'"/>';
   return $html;
}

function getCombinedStylesheetURL(){
  $output = "";
  if(isset($_SERVER["stylesheet_queue"]) and is_array($_SERVER["stylesheet_queue"])){
  $files = implode(";", $_SERVER["stylesheet_queue"]);
     $url = "?output_stylesheets=".$files;
  } else {
     $url="index.php?stylesheets=";
  }
   
   return $url;
}