<?php
class SettingsCache {
	private static $settings = array ();
	public static function get($key) {
		if (isset ( self::$settings [$key] )) {
			return self::$settings [$key];
		}
		return null;
	}
	public static function set($key, $value) {
		if ($value === null and isset ( self::$settings [$key] )) {
			unset ( self::$settings [$key] );
		} else {
			self::$settings [$key] = $value;
		}
	}
}
