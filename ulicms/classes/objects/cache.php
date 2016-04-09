<?php
class Cache {
	// Alle Caches leeren
	// Sowohl den Seiten-Cache, den Download/Paketmanager Cache
	// als auch den APC Bytecode Cache
	public static function clear() {
		add_hook ( "before_clear_cache" );
		$cache_type = Settings::get ( "cache_type" );
		// Es gibt zwei verschiedene Cache Modi
		// Cache_Lite und File
		// Cache_Lite leeren
		if ($cache_type === "cache_lite" and class_exists ( "Cache_Lite" )) {
			$Cache_Lite = new Cache_Lite ( $options );
			$Cache_Lite->clean ();
		} else {
			// File leeren
			$cache_dir = Path::resolve ( "ULICMS_CACHE" );
			Path::removeDir ( $cache_dir, false );
		}
		
		if (function_exists ( "apc_clear_cache" )) {
			self::clearAPC ();
		}
		
		add_hook ( "after_clear_cache" );
	}
	public static function clearAPC() {
		if (! function_exists ( "apc_clear_cache" )) {
			return false;
		}
		apc_clear_cache ();
		apc_clear_cache ( 'user' );
		apc_clear_cache ( 'opcode' );
	}
}