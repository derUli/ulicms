<?php
define("LOG_TIME_FORMAT", "H:i:s");
function log_db_query($query){
     // Das DB Logging kann man deaktivieren, durch anlegen der Konfigurationsvariable disable_query_log in der cms_config.php;
    include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "cms-config.php";
    
    
     $config = new config();
     if(!isset($config -> query_logging)){
         return false;
        
         }else{
        
         if(!$config -> query_logging)
             return false;
         }
    
     $logdir = "";
    
     if(is_admin_dir())
         $logdir .= "../";
    
     $logdir .= "content/log/db/";
    
     if(!is_dir($logdir)){
         @mkdir($logdir, 0755, true);
         if(!is_dir($logdir))
             return false;
         }
    
     @$date = date("Y-m-d");
     @$time = date(LOG_TIME_FORMAT);
     $logfile = $logdir . $date . ".log";
     $query = trim($query);
    
     // Make all Line Endings Windows Style
    $query = preg_replace('~\r\n?~', "\r\n", $query);
    
     $handle = fopen($logfile, "a");
     fwrite($handle, $time . "\t");
     fwrite($handle, $query);
     fwrite($handle, "\r\n");
     fclose($handle);
     return true;
    
     }
