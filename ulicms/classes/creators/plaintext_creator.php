<?php
class PlainTextCreator {
	var $target_file = null;
	var $content = null;
	var $title = null;
	public function __construct() {
		$this->cached_file = buildCacheFilePath ( $_SERVER ["REQUEST_URI"] );
		ob_start ();
		echo get_title ();
		echo "\r\n";
		echo "\r\n";
		content ();
		$this->content = ob_get_clean ();
	}
	private function httpHeader() {
		header ( "Content-type: text/plain; charset=UTF-8" );
	}
	public function output() {
		$hasModul = containsModule ( get_requested_pagename () );
		if (! Settings::get ( "cache_disabled" ) and getenv ( 'REQUEST_METHOD' ) == "GET" and ! $hasModul) {
			if (getCacheType () == "file") {
				if (file_exists ( $this->cached_file )) {
					$last_modified = filemtime ( $this->cached_file );
					if (time () - $last_modified < CACHE_PERIOD) {
						$this->httpHeader ();
						readfile ( $this->cached_file );
						exit ();
					} else {
						@unlink ( $this->cached_file );
					}
				}
			}
		}
		ob_start ();
		autor ();
		$author = ob_get_clean ();
		$data [] = array (
				"Title",
				"Content",
				"Meta Description",
				"Meta Keywords",
				"Author" 
		);
		$data = array ();
		$this->content = br2nlr ( $this->content );
		$this->content = strip_tags ( $this->content );
		$this->content = str_replace ( "\r\n", "\n", $this->content );
		$this->content = str_replace ( "\r", "\n", $this->content );
		$this->content = str_replace ( "\n", "\r\n", $this->content );
		$this->content = unhtmlspecialchars ( $this->content );
		$this->content = preg_replace_callback ( '/&#([0-9a-fx]+);/mi', 'replace_num_entity', $this->content );
		if (! Settings::get ( "cache_disabled" ) and getenv ( 'REQUEST_METHOD' ) == "GET" and ! $hasModul) {
			if (getCacheType () == "file") {
				$handle = fopen ( $this->cached_file, "w" );
				fwrite ( $handle, $this->content );
				fclose ( $handle );
			}
		}
		$this->httpHeader ();
		echo $this->content;
		exit ();
	}
}
