<?php 
// Wrapper für mysql_query
// loggt die Query mit
function db_query($query){
   if(is_admin_dir())
      include_once "../lib/logger.php";
   else 
      include_once "lib/logger.php";
   log_db_query($query);
   return mysql_query($query);
   
}
?>