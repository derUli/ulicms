<?php 
define("LOG_TIME_FORMAT", "H:i:s");
function log_db_query($query){
   // Das DB Logging kann man deaktivieren, durch anlegen der Konfigurationsvariable disable_query_log in der cms_config.php;
   if(is_admin_dir())
      include_once "../cms-config.php";
   else
      include_once "cms-config.php";
   $config = new config();
   if(!isset($config->query_logging)){
      if($config->query_logging)
         return false;
      
   }
   
   $logdir = "";
   
   if(is_admin_dir())
       $logdir .= "../";
       
   $logdir .= "content/log/db/";

   if(!is_dir($logdir)){
      @mkdir($logdir, 077, true);
      if(!is_dir($logdir))
         return false;   
   }
   
   @$date = date("Y-m-d");
   @$time = date(LOG_TIME_FORMAT);
   $logfile = $logdir.$date.".log";
   $query = trim($query);
   
   // Make all Line Endings Windows Style
   $query = preg_replace('~\r\n?~', "\r\n", $query);
   
   $handle = fopen($logfile, "a");
   fwrite($handle, $time."\t");
   fwrite($handle, $query);
   fwrite($handle, "\r\n");
   return true;
   
}