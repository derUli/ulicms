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
	public static function getCacheType() {
		$cache_type = "file";
		$c = Settings::get ( "cache_type" );
		switch ($c) {
			case "cache_lite" :
				@include "Cache/Lite.php";
				$cache_type = "cache_lite";
				
				break;
			case "file" :
			default :
				$cache_type = "file";
				break;
				break;
		}
		return $cache_type;
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
		if ($type == "cache_lite") {
			self::storeCacheLite ( $data, $requestUri );
		} else {
			self::storeFile ( $data, $requestUri );
		}
	}
	public static function get($requestUri = null) {
		$type = self::getCacheType ();
		if ($type == "cache_lite") {
			return self::getCacheLite ( $requestUri );
		} else {
			return self::getFile ( $requestUri );
		}
	}
	private static function getCacheLite($data, $requestUri = null) {
		if (! $request_uri) {
			$requestUri = get_request_uri ();
		}
		
		$cacheFile = md5 ( self::buildCacheFilePath ( $requestUri ) );
		
		$options = array (
				'lifeTime' => Settings::get ( "cache_period" ),
				'cacheDir' => "content/cache/" 
		);
		if (! class_exists ( "Cache_Lite" )) {
			throw new Exception ( "Fehler:<br/>Cache_Lite ist nicht installiert. Bitte stellen Sie den Cache bitte wieder auf Datei-Modus um." );
		}
		
		$Cache_Lite = new Cache_Lite ( $options );
		return $Cache_Lite->get ( $cacheFile );
	}
	private static function storeCacheLite($data, $requestUri = null) {
		if (! $request_uri) {
			$requestUri = get_request_uri ();
		}
		
		$cacheFile = md5 ( self::buildCacheFilePath ( $requestUri ) );
		
		$options = array (
				'lifeTime' => Settings::get ( "cache_period" ),
				'cacheDir' => "content/cache/" 
		);
		if (! class_exists ( "Cache_Lite" )) {
			throw new Exception ( "Fehler:<br/>Cache_Lite ist nicht installiert. Bitte stellen Sie den Cache bitte wieder auf Datei-Modus um." );
		}
		
		$Cache_Lite = new Cache_Lite ( $options );
		$Cache_Lite->save ( $data, $cacheFile );
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
		return "content/cache/" . md5 ( $unique_identifier ) . ".tmp";
	}
}