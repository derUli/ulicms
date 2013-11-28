<?php 
require_once(ULICMS_ROOT.DIRECTORY_SEPERATOR."lib".DIRECTORY_SEPERATOR."MPDF57".DIRECTORY_SEPERATOR."mpdf.php");
class PDFCreator{
   var $target_file = null;
   var $content = null;
   var $language = null;
   var $paper_format = "A4";
   public function __construct(){
     $this->cached_file = buildCacheFilePath($_SESSION["REQUEST_URI"].".pdf");
     ob_start();    
     echo "<h1>".get_title()."</h1>";
     content();
     $this->content = ob_get_clean();
     
   }
   
    private function httpHeader(){
    header("Content-type: application/pdf; charset=UTF-8");

    }
   
   public function output(){     
      $hasModul = containsModule(get_requested_pagename());

      if(!getconfig("cache_disabled")
         and getenv('REQUEST_METHOD') == "GET" and 
         !$hasModul){   
     
   
      if(file_exists($this->cached_file)){
         $last_modified = filemtime($this->cached_file);  
          if(time() - $last_modified < CACHE_PERIOD){
          $this->httpHeader();
          exit();
          }    else {
        @unlink($this->cached_file);
      } 

      } 
      }
      
      

      $mpdf = new mPDF(getCurrentLanguage(true), 'A4');
      $mpdf->WriteHTML($this->content);
      $mpdf->Output($this->cached_file);
      
      $this->httpHeader();
      readfile($this->cached_file);
      exit();
   }
}