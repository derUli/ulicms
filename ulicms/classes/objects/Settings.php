<?php
class Settings{
	public static function register($key, $value){
	   self::init($key, $value);
	}

   public static function init($key, $value) {
	$retval = false;
	if (! getconfig ( $key )) {
		setconfig ( $key, $value );
		$retval = true;
	}
	return $retval;
}

	// get a config variable
	public static function get($key) {
		if (isset ( $GLOBALS ['settings_cache'] [$key] )) {
			return $GLOBALS ['settings_cache'] [$key];
		}
		$env_key = "ulicms_" . $key;
		$env_var = getenv ( $env_key );
		if ($env_var)
			return $env_var;
		$ikey = db_escape ( $key );
		$query = db_query ( "SELECT value FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
		if (db_num_rows ( $query ) > 0) {
			while ( $row = db_fetch_object ( $query ) ) {
				$GLOBALS ['settings_cache'] [$key] = $row->value;
				return $row->value;
			}
		} else {
			$GLOBALS ['settings_cache'] [$key] = false;
			return false;
		}
	}
	
	public static function getLang($name, $lang) {
		$retval = false;
		$config = getconfig ( $name . "_" . $lang );
		if ($config)
			$retval = $config;
		else
			$config = getconfig ( $name );
		return $config;
	}
	
	// Set a configuration Variable;
	public static function set($key, $value) {
		$query = db_query ( "SELECT id FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
		
		if (db_num_rows ( $query ) > 0) {
			db_query ( "UPDATE " . tbname ( "settings" ) . " SET value='$value' WHERE name='$key'" );
		} else {
			
			db_query ( "INSERT INTO " . tbname ( "settings" ) . " (name, value) VALUES('$key', '$value')" );
		}
		
		if (isset ( $GLOBALS ['settings_cache'] [$key] )) {
			unset ( $GLOBALS ['settings_cache'] [$key] );
		}
	}
	
	// Remove an configuration variable
	public static function delete($key) {
		$key = db_escape ( $key );
		db_query ( "DELETE FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
		return db_affected_rows () > 0;
	}
}