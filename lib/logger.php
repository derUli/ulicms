<?php 
function log_db_query($query){
   // Das DB Logging kann man deaktivieren, durch anlegen der Konfigurationsvariable disable_query_log in der cms_config.php;
   
   include_once "cms-config.php";
   $config = new config();
   if(!isset($config->query_logging))
      return false;
   
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
   $logfile = $logdir.$date.".log";
   $query = trim($query);
   
   // Make all Line Endings Windows Style
   $query = preg_replace('~\r\n?~', "\r\n", $query);
   
   $handle = fopen($logfile, "a");
   fwrite($handle, $query);
   fwrite($handle, "\r\n");
   
}