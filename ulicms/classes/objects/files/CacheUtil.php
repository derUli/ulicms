<?php
use phpFastCache\Helper\Psr16Adapter;
class CacheUtil {
	private static $adapter;
	public static function getAdapter() {
		if (! self::isCacheEnabled ()) {
			return null;
		}
		
		if (! is_null ( self::$adapter )) {
			return self::$adapter;
		}
		
		$cacheConfig = array (
				"path" => Path::resolve ( "ULICMS_CACHE" ),
				"defaultTtl" => self::getCachePeriod ()
		);
		
		// If SQLite available use it
		// else use file
		// TODO: Add other fallback methods
		$driver = "files";
		if (function_exists ( 'sqlite_open' )) {
			$driver = "sqlite";
		}
		
		self::$adapter = new phpFastCache\Helper\Psr16Adapter ( $driver, $cacheConfig );
		
		return self::$adapter;
	}
	public static function isCacheEnabled() {
		return (! Settings::get ( "cache_disabled" ) && !is_logged_in());
	}
	public static function clearCache() {
		add_hook ( "before_clear_cache" );
		
		// clear apc cache if available
		if (function_exists ( "apc_clear_cache" )) {
			clearAPCCache ();
		}
		// clear opcache if available
		if (function_exists ( "opcache_reset" )) {
			opcache_reset ();
		}
		
		$adapter = self::getAdapter ();
		if ($adapter) {
			$adapter->clear ();
		}
		
		SureRemoveDir ( Path::resolve ( "ULICMS_CACHE" ), false );
				
		// Sync modules table in database with modules folder
		$moduleManager = new ModuleManager ();
		$moduleManager->sync ();
		
		add_hook ( "after_clear_cache" );
	}
	// Return cache period in seconds
	public static function getCachePeriod() {
		return Settings::get ( "cache_period", "int" );
	}
	public static function getCurrentUid() {
		return md5 ( get_request_uri () . getCurrentLanguage () . boolval ( is_mobile () ) );
	}
}