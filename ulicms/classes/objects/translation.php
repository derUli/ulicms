<?php
// This class is currently a work in progress
// It's currently only used for overriding of translation
class Translation {
	private static $translations = null;
	public static function init() {
		$translations = array ();
	}
	public static function set($key, $value) {
		$key = "translation_" . $key;
		$key = strtoupper ( $key );
		self::$translations [$key] = $value;
	}
	public static function get($key) {
		$retval = null;
		if (isset ( self::$translations [$key] )) {
			$retval = self::$translations [$key];
		}
		return $retval;
	}
}