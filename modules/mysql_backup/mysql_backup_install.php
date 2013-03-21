<?php 

function mysql_backup_check_install(){
  if(getconfig("mysql_backup_every_days") === false){
     // Standardeinstellung, Backup alle 7 Tage
     $mysql_Backup_every_days_default = 7;
     setconfig("mysql_backup_every_days", 
               $mysql_Backup_every_days_default);
  }
  
   if(getconfig("mysql_backup_last_time") === false){
       setconfig("mysql_backup_last_time", 0);
   }
   
   $path_to_backup_dir = path_to_backup_dir(); 
   
   if(!file_exists($path_to_backup_dir)){
      @mkdir($path_to_backup_dir);
   } 
   else 
   {
   $mod = substr(decoct(fileperms($path_to_backup_dir)), 2);
   if(intval($mod) < 755){
      @chmod($path_to_backup_dir, 0777);
   }
   
   }
   

   
   $tmpfile = path_to_backup_dir().uniqid();
   $writable = @file_put_contents($tmpfile, 
"test") !== false;

   if($writable)
      @unlink($tmpfile);


   
   if(file_exists($path_to_backup_dir) and $writable){
      $htaccess_file = $path_to_backup_dir.".htaccess";
      if(!file_exists($htaccess_file)){
         $handle = fopen( $htaccess_file, "w");
         fwrite($handle, "Deny from all");
         fclose($handle);
                                          
      }
   }
   
   
   }
   
   
     
   
   
   function path_to_backup_dir(){
      if(file_exists("backend.php")){
         return "../backup/";
      }
      
      return "backup/";
   }
   
  
  
?>