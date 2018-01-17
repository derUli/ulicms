<?php
class Cache {
	// Alle Caches leeren
	// Sowohl den Seiten-Cache, den Download/Paketmanager Cache
	// als auch den APC Bytecode Cache
	public static function clear() {
		add_hook ( "before_clear_cache" );
		$cache_type = self::getCacheType ();
		
		switch ($cache_type) {
			case "file" :
			default :
				SureRemoveDir ( PATH::Resolve ( "ULICMS_CACHE" ), false );
				break;
				break;
		}
		
		// clear apc cache if available
		self::clearAPC ();
		// clear opcache if available
		if (function_exists ( "opcache_reset" )) {
			opcache_reset ();
		}
		
		// Sync modules table in database with modules folder
		$moduleManager = new ModuleManager ();
		$moduleManager->sync ();
		
		add_hook ( "after_clear_cache" );
	}
	public static function getCacheType() {
		return Settings::get ( "cache_type" );
	}
	public static function clearAPC() {
		if (! function_exists ( "apc_clear_cache" )) {
			return false;
		}
		apc_clear_cache ();
		apc_clear_cache ( 'user' );
		apc_clear_cache ( 'opcode' );
	}
	public static function store($data, $requestUri = null) {
		$type = self::getCacheType ();
		switch ($type) {
			case "file" :
			default :
				self::storeFile ( $data, $requestUri );
				break;
				break;
		}
	}
	public static function get($requestUri = null) {
		$type = self::getCacheType ();
		switch ($type) {
			case "file" :
			default :
				return self::getFile ( $requestUri );
				break;
				break;
		}
		return null;
	}
	private static function storeFile($data, $requestUri = null) {
		if (! $request_uri) {
			$requestUri = get_request_uri ();
		}
		$cacheFile = self::buildCacheFilePath ( $requestUri );
		file_put_contents ( $cacheFile, $data );
	}
	private static function getFile($requestUri = null) {
		$retval = null;
		if (! $request_uri) {
			$requestUri = get_request_uri ();
		}
		$cacheFile = self::buildCacheFilePath ( $requestUri );
		if (file_exists ( $cacheFile )) {
			$lastModified = filemtime ( $cacheFile );
			if (time () - $lastModified < CACHE_PERIOD) {
				$retval = file_get_contents ( $cacheFile );
			}
		}
		return $retval;
	}
	public static function buildCacheFilePath($request_uri) {
		$language = $_SESSION ["language"];
		if (! $language) {
			$language = Settings::get ( "default_language" );
		}
		$unique_identifier = $request_uri . $language . strbool ( is_mobile () );
		if (function_exists ( "apply_filter" )) {
			$unique_identifier = apply_filter ( $unique_identifier, "unique_identifier" );
		}
		return Path::resolve ( "ULICMS_CACHE/" . md5 ( $unique_identifier ) . ".tmp" );
	}
}