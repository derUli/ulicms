<?php
class Settings {
	public static function register($key, $value) {
		self::init ( $key, $value );
	}
	public static function init($key, $value) {
		$retval = false;
		if (! self::get ( $key )) {
			self::set ( $key, $value );
			$retval = true;
			$GLOBALS ['settings_cache'] [$key] = $value;
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
		if ($env_var) {
			return $env_var;
		}
		$key = db_escape ( $key );
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

	public static function output($key){
      $value = self::get($key);
			if($value){
		    	echo $value;
		}
	}

	public static function outputEscaped($key){
			$value = self::get($key);
			if($value){
			  echo htmlspecialchars($value, ENT_QUOTES, "UTF-8");;
			}
	}

	public static function getLang($name, $lang) {
		$retval = false;
		$config = self::get ( $name . "_" . $lang );
		if ($config)
			$retval = $config;
		else
			$config = self::get ( $name );
		return $config;
	}

	// Set a configuration Variable;
	public static function set($key, $value) {
		$key = db_escape ( $key );
		$value = db_escape ( $value );
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
