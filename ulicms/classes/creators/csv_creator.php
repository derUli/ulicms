<?php
class CSVCreator {
	var $target_file = null;
	var $content = null;
	var $title = null;
	public function __construct() {
		$this->cached_file = Cache::buildCacheFilePath ( get_request_uri () );
		$this->title = get_title ();
		ob_start ();
		content ();
		$this->content = ob_get_clean ();
	}
	private function httpHeader() {
		header ( "Content-type: text/csv; charset=UTF-8" );
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
		$data = array ();
		$data [] = array (
				"Title",
				"Content",
				"Meta Description",
				"Meta Keywords",
				"Author" 
		);
		$this->content = str_replace ( "\r\n", "\n", $this->content );
		$this->content = str_replace ( "\r", "\n", $this->content );
		$this->content = str_replace ( "\n", " ", $this->content );
		$data [] = array (
				$this->title,
				$this->content,
				get_meta_description (),
				get_meta_keywords (),
				$author 
		);
		$csv_string = getCSV ( $data [0] );
		$csv_string .= getCSV ( $data [1] );
		if (! Settings::get ( "cache_disabled" ) and getenv ( 'REQUEST_METHOD' ) == "GET" and ! $hasModul) {
			if (getCacheType () == "file") {
				$handle = fopen ( $this->cached_file, "w" );
				fwrite ( $handle, $csv_string );
				fclose ( $handle );
			}
		}
		$this->httpHeader ();
		echo $csv_string;
		exit ();
	}
}
