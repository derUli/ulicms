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

function db_fetch_field($result){
   return mysql_fetch_field($result);
}

function db_fetch_assoc($result){
   return mysql_fetch_assoc($result);
}

function db_close(){
   mysql_close();
}

// Connect with database server
function db_connect($server, $user, $password){
   $result = mysql_connect($server, $user, $password);
   if(!$result)
      return false;
    db_query("SET NAMES 'utf8'");
    return $result;
}
// Datenbank auswählen
function db_select($schema){
   return mysql_select_db($schema);
}

function db_num_fields($result){
   return mysql_num_fields($result);
}

function db_affected_rows(){
   return mysql_affected_rows();
}

function schema_select($schema){
  return db_select($schema);
}

function db_select_db($schema){
  return schema_select($schema);
}


function db_fetch_object($result){
   return mysql_fetch_object($result);
}

function db_fetch_row($result){
   return mysql_fetch_row($result);
}

function db_num_rows($result){
   return mysql_num_rows($result);
}

function db_last_error(){
   return mysql_error();
}

function db_real_escape_string($value){
   return mysql_real_escape_string($value);
}

define("DB_TYPE_INT", 1);
define("DB_TYPE_FLOAT", 2);
define("DB_TYPE_STRING", 3);
define("DB_TYPE_BOOL", 4);


// Abstraktion für Escapen von Werten
function db_escape($value, $type = null){
    if(is_null($type)){

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

	  
	  } else {
	  if($type === DB_TYPE_INT) {
	     return intval($value);
	  } else if($type === DB_TYPE_FLOAT) {
	     return floatval($value);
	  } else if($type === DB_TYPE_STRING) {
	     return mysql_real_escape_string($value);
	  } else if($type === DB_TYPE_BOOL) {
	     return intval($value);
	  } else {
	     return $value;
	  } 
	  
	  
	  }
	  
}

?>