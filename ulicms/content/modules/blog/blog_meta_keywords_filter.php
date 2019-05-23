<?php
function blog_meta_keywords_filter($txt) {
	$single = db_escape ( $_GET ["single"] );
	$query = db_query ( "SELECT content_full, meta_keywords FROM `" . tbname ( "blog" ) . "` WHERE seo_shortname='$single'" );
	
	if (! $query) {
		return $txt;
	}
	
	$content_full = false;
	
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_assoc ( $query );
		$content_full = $result ["content_full"];
		if (! is_null ( $result ["meta_keywords"] ) and ! empty ( $result ["meta_keywords"] )) {
			$meta_keywords = trim ( $result ["meta_keywords"] );
			return real_htmlspecialchars ( $meta_keywords );
		}
	}
	
	if (! containsModule ( get_requested_pagename (), "blog" ) or ! $single or ! $content_full) {
		return $txt;
	}
	
	$maxlength_chars = 160;
	$content_full = strip_tags ( $content_full );
	
	// $shortstring = preg_replace('/(?:[ \t]*(?:\n|\r\n?)){2,}/', "\n", $shortstring);
	// Leerzeichen und Zeilenumbrüche entfernen
	$content_full = trim ( $content_full );
	$content_full = preg_replace ( "#[ ]*[\r\n\v]+#", "\r\n", $content_full );
	$content_full = preg_replace ( "#[ \t]+#", " ", $content_full );
	$content_full = str_replace ( "\r\n", " ", $content_full );
	$content_full = str_replace ( "\n", " ", $content_full );
	$content_full = str_replace ( "\r", " ", $content_full );
	$content_full = str_replace ( "&nsbp;", " ", $content_full );
	
	$content_full = trim ( $content_full );
	
	$stripped_content = trim ( $content_full );
	$stripped_content = str_replace ( "\\r\\n", "\r\n", $stripped_content );
	$stripped_content = strip_tags ( $stripped_content );
	$words = keywordsFromString ( $stripped_content );
	$maxWords = 10;
	$currentWordCount = 0;
	$maxi = count ( $words );
	$i = 0;
	$meta_keywords = Array ();
	if (count ( $words ) > 0) {
		foreach ( $words as $key => $value ) {
			$i ++;
			$key = trim ( $key );
			
			if (! empty ( $key ) and $currentWordCount <= $maxWords) {
				$currentWordCount ++;
				array_push ( $meta_keywords, $key );
			}
		}
	}
	
	$meta_keywords = implode ( ", ", $meta_keywords );
	$meta_keywords = unhtmlspecialchars ( $meta_keywords );
	return $meta_keywords;
}
?>