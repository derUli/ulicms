<?php
use phpFastCache\CacheManager;
use phpFastCache\Helper\Psr16Adapter;
class CacheUtil {
	public static function getAdapter() {
		if (! self::isCacheEnabled ()) {
			return null;
		}
		
		$cacheConfig = array (
				"path" => PATH::resolve ( "ULICMS_CACHE" ) 
		);
		
		// Until now caching method is always "files"
		// TODO: Implement Other Caching Methods
		
		$Psr16Adapter = new Psr16Adapter ( "files", $cacheConfig );
		
		return $Psr16Adapter;
	}
	public static function isCacheEnabled() {
		return (is_null ( Settings::get ( "cache_disabled" ) ));
	}
	public static function clearCache() {
		add_hook ( "before_clear_cache" );
		
		trigger ( E_USER_WARNING, "FIXME: Reimplement Page Caching using PhpFastCache" );
		
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
		
		// Sync modules table in database with modules folder
		$moduleManager = new ModuleManager ();
		$moduleManager->sync ();
		
		add_hook ( "after_clear_cache" );
	}
	// Return cache period in seconds
	public static function getCachePeriod() {
		return Settings::get ( "cache_period", "int" );
	}
}