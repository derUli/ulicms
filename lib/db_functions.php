<?php 
// Wrapper für mysql_query
// loggt die Query mit
function db_query($query){
   include_once "lib/logger.php";
   log_db_query($query);
   return mysql_query($query);
   
}
?>