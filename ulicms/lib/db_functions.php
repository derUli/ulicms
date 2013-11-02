<?php
// Abstraktion für Ausführen von SQL Strings
function db_query($query){
     if(is_admin_dir())
         include_once "../lib/logger.php";
     else
         include_once "lib/logger.php";
     log_db_query($query);
     return mysql_query($query);
    

}

function db_num_rows($query){
   return mysql_num_rows($query);
}

function db_last_error(){
   return mysql_error();
}

// Abstraktion für Escapen von Werten
function db_escape($value){
	if(is_float($value)){
      return floatval($value);
	}
	else if(is_int($value)){
	  return intval($value);
	}
	else if(is_bool($value)){
	  return (int) $value;
	}
	else{
	  return mysql_real_escape_string($value);
	  }

}

?>