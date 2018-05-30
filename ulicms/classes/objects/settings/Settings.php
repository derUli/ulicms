<?php
class Settings {
	public static function register($key, $value, $type = 'str') {
		self::init ( $key, $value, $type );
	}
	public static function init($key, $value, $type = 'str') {
		$retval = false;
		if (! self::get ( $key )) {
			self::set ( $key, $value, $type );
			$retval = true;
			SettingsCache::set ( $key, $value );
		}
		return $retval;
	}
	public static function preloadAll() {
		$query = db_query ( "SELECT name, value FROM " . tbname ( "settings" ) );
		while ( $result = Database::fetchObject ( $result ) ) {
			SettingsCache::set ( $result->name, $result->value );
		}
	}
	// get a config variable
	public static function get($key, $type = 'str') {
		if (!is_null(SettingsCache::get ( $key ))) {
			return SettingsCache::get ( $key );
		}
		$key = db_escape ( $key );
		$query = db_query ( "SELECT value FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
		if (db_num_rows ( $query ) > 0) {
			while ( $row = db_fetch_object ( $query ) ) {
				$value = self::convertVar ( $row->value, $type );
				SettingsCache::set ( $key, $value, $type );
				return $value;
			}
		} else {
			SettingsCache::set ( $key, null );
			return false;
		}
	}
	public static function output($key, $type = 'str') {
		$value = self::get ( $key, $type );
		if ($value) {
			echo $value;
		}
	}
	public static function outputEscaped($key, $type = 'str') {
		$value = self::get ( $key, $type );
		if ($value) {
			echo htmlspecialchars ( $value, ENT_QUOTES, "UTF-8" );
		}
	}
	public static function getLang($name, $lang, $type = 'str') {
		$retval = false;
		$config = self::get ( $name . "_" . $lang, $type );
		if ($config) {
			$retval = $config;
		} else {
			$config = self::get ( $name, $type );
		}
		return $config;
	}
	// Set a configuration Variable;
	public static function set($key, $value, $type = 'str') {
		$key = db_escape ( $key );
		$value = self::convertVar ( $value, $type );
		$value = db_escape ( $value );
		$query = db_query ( "SELECT id FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
		if (db_num_rows ( $query ) > 0) {
			db_query ( "UPDATE " . tbname ( "settings" ) . " SET value='$value' WHERE name='$key'" );
		} else {
			db_query ( "INSERT INTO " . tbname ( "settings" ) . " (name, value) VALUES('$key', '$value')" );
		}
		SettingsCache::set ( $key, null );
	}
	// Remove an configuration variable
	public static function delete($key) {
		$key = db_escape ( $key );
		db_query ( "DELETE FROM " . tbname ( "settings" ) . " WHERE name='$key'" );
		SettingsCache::set ( $key, null );
		return db_affected_rows () > 0;
	}
	public static function convertVar($value, $type) {
		switch ($type) {
			case 'str' :
				$value = strval ( $value );
				break;
			case 'int' :
				$value = intval ( $value );
				break;
			case 'float' :
				$value = floatval ( $value );
				break;
			case 'bool' :
				$value = intval ( boolval ( $value ) );
				break;
		}
		return $value;
	}
	public static function getAll($order = "name") {
		$result = array ();
		$query = Database::query ( "SELECT * FROM `{prefix}settings` order by $order", true );
		while ( $result [] = Database::fetchObject ( $query ) ) {
		}
		return $result;
	}
	public static function mappingStringToArray($str) {
		$str = trim ( $str );
		$str = normalizeLN ( $str, "\n" );
		$lines = explode ( "\n", $str );
		$lines = array_map ( 'trim', $lines );
		$lines = array_filter ( $lines, 'strlen' );
		$result = array ();
		foreach ( $lines as $line ) {
			// if a line starts with a hash skip it (comment)
			if (startsWith ( $line, "#" )) {
				continue;
			}
			$splitted = explode ( "=>", $line );
			$splitted = array_map ( 'trim', $splitted );
			$key = $splitted [0];
			$value = $splitted [1];
			$result [$key] = $value;
		}
		return $result;
	}
}