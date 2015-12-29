<?php
class Path {
	public static function resolve($path) {
		$path = str_ireplace ( "ULICMS_ROOT", ULICMS_ROOT, $path );
		$path = str_ireplace ( "ULICMS_TMP", ULICMS_TMP, $path );
		$path = str_ireplace ( "ULICMS_CACHE", ULICMS_CACHE, $path );
		$path = str_ireplace ( "\\", "/", $path );
		$path = rtrim ( $path, "/" );
		return $path;
	}
}