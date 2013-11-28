<?php 
class CSVCreator{
   var $target_file = null;
   var $content = null;
   var $title = null;
   public function __construct(){
     $this->cached_file = buildCacheFilePath($_SESSION["REQUEST_URI"]);
     $this->title = get_title();
     ob_start();  
     content();
     $this->content = ob_get_clean();

     
   }
   
   private function httpHeader(){
    header("Content-type: text/csv; charset=UTF-8");
    header('Content-Disposition: attachment; filename="'.get_requested_pagename().'.csv"'); 
   
   }
   
   public function output(){     
      $hasModul = containsModule(get_requested_pagename());

      if(!getconfig("cache_disabled")
         and getenv('REQUEST_METHOD') == "GET" and 
         !$hasModul){   
     
     if(getCacheType() == "file"){
      
      if(file_exists($this->cached_file)){
         $last_modified = filemtime($this->cached_file);  
          if(time() - $last_modified < CACHE_PERIOD){
          $this->httpHeader();
          readfile($this->cached_file);
          exit();
          }    
         else {
           @unlink($this->cached_file);
      } 
      
      
      } 
      
      } else if(getCacheType() == "cache_lite"){
       $id = md5($_SESSION["REQUEST_URI"]);
       $options = array(
'lifeTime' => getconfig("cache_period"));
if(!class_exists("Cache_Lite")){
 throw new Exception("Fehler:<br/>Cache_Lite ist nicht installiert. Bitte stellen Sie den Cache bitte wieder auf Datei-Modus um.");
}

$Cache_Lite = new Cache_Lite($options);

if ($data = $Cache_Lite -> get($id)){
$this->httpHeader();
 die($data);
}






      }
      }

      ob_start();
      autor();
      $author = ob_get_clean();
      
      $data[] = array("Title", "Content", "Meta Description", "Meta Keywords", "Author");
      $data = array();
      $this->content = str_replace("\n\n", "", $this->content);
      $data[] = array($this->title, $this->content, meta_description(), meta_keywords(), $author);
      
      $csv_string = getCSV($data[0]);
      $csv_string .= getCSV($data[1]);
      
       if(!getconfig("cache_disabled")
         and getenv('REQUEST_METHOD') == "GET" and 
         !$hasModul){   
         if(getCacheType() == "file"){
            $handle = fopen($this->cached_file, "w");
            fwrite($handle, $csv_string);
            fclose($handle);
            }
         else if(getCacheType() == "cache_lite"){
            $Cache_Lite -> save($data, $id);
         }
         }
     
      
      $this->httpHeader();
      echo $csv_string;
      exit();
   }
}