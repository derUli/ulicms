<?php 
function log_db_query($query){
   // Das DB Logging kann man deaktivieren, durch anlegen der Konfigurationsvariable disable_query_log
   if(getconfig("disable_db_query_log"))
      return false;
   
   $logdir = "";
   
   if(is_admin_dir())
       $logdir .= "../";
       
   
   $logdir .= "content/log/db/";
   
   if(!is_dir($logdir)){
      @mkdir($logdir, $recursive = true);
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