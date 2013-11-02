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


// Fetch Row in diversen Datentypen

function db_fetch_array($result){
   return mysql_fetch_array($result);
}

function db_fetch_assoc($result){
   return db_fetch_assoc($result);
}

function db_close(){
   mysql_close();
}

// Connect with database server
function db_connect($server, $user, $password){
   return mysql_connect($server, $user, $password);
}
// Datenbank auswählen
function db_select($schema){
   return mysql_select_db($schema);
}

function schema_select($schema){
  return db_select($schema);
}

function db_select_db($schema){
  return db_select_db($schema);
}


function db_fetch_object($result){
   return db_fetch_object($result);
}

function db_fetch_row($result){
   return db_fetch_row($result);
}

function db_num_rows($result){
   return mysql_num_rows($result);
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