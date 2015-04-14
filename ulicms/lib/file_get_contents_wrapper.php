<?php
// die Funktionalität von file_get_contents
// mit dem Curl-Modul umgesetzt
function file_get_contents_curl($url) {
	$ch = curl_init ();
	
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ); // Set curl to return the data instead of printing it to the browser.
	curl_setopt ( $ch, CURLOPT_URL, $url );
	
	$data = curl_exec ( $ch );
	
	if (curl_getinfo ( $ch, CURLINFO_HTTP_CODE ) != 200 and curl_getinfo ( $ch, CURLINFO_HTTP_CODE ) != 304) {
		$data = false;
	}
	
	curl_close ( $ch );
	return $data;
}
function is_url($url) {
	if (substr_compare ( $url, 'http://', 0, 7 ) > 0 or substr_compare ( $url, 'https://', 0, 8 ) > 0 or substr_compare ( $url, 'ftp://', 0, 8 ) > 0) {
		return true;
	}
	
	return false;
}

// Wrapper um file_get_contents
// Falls allow_url_fopen deaktiviert ist,
// wird CURL alls Fallback genutzt, falls vorhanden.
// Ansonsten wird false zurückgegeben.
function file_get_contents_wrapper($url, $no_cache = false) {
	$cache_name = md5 ( $url ) . "-" . basename ( $url );
	$cache_folder = ULICMS_ROOT . "/content/cache";
	$cache_path = $cache_folder . "/" . $cache_name;
	if (file_exists ( $cache_path ) && is_url ( $url ) && ! $no_cache)
		return file_get_contents ( $cache_path );
	
	if (ini_get ( "allow_url_fopen" ) or ! is_url ( $url )) {
		$content = file_get_contents ( $url );
	} else if (function_exists ( "curl_init" ) and is_url ( $url )) {
		$content = file_get_contents_curl ( $url );
	}
	if ($content) {
		if (is_dir ( $cache_folder ) and is_url ( $url ) and ! $no_cache)
			file_put_contents ( $cache_path, $content );
		
		return $content;
	}
	return false;
}
function url_exists($url) {
	if (@file_get_contents ( $url, FALSE, NULL, 0, 0 ) === false)
		return false;
	return true;
}
