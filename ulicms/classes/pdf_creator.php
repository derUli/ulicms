<?php
$mpdf = ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "MPDF60" . DIRECTORY_SEPERATOR . "mpdf.php";
if(file_exists($mpdf)){
   require_once ($mpdf);
}

class PDFCreator {
	var $target_file = null;
	var $content = null;
	var $language = null;
	var $paper_format = "A4";
	public function __construct() {
		$this->cached_file = buildCacheFilePath ( $_SERVER ["REQUEST_URI"] );
		ob_start ();
		echo "<h1>" . get_title () . "</h1>";
		content ();
		$this->content = ob_get_clean ();
	}
	private function httpHeader() {
		header ( "Content-type: application/pdf; charset=UTF-8" );
	}
	public function output() {
		$hasModul = containsModule ( get_requested_pagename () );
		
		if (! getconfig ( "cache_disabled" ) and getenv ( 'REQUEST_METHOD' ) == "GET" and ! $hasModul) {
			
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
		if(!class_exists("mPDF")){
		    echo "mPDF not installed. Please install <a href=\"http://www.ulicms.de/mpdf_supplement.html\" target=\"_blank\">mPDF supplement</a>.";
			die();
		}
		$mpdf = new mPDF ( getCurrentLanguage ( true ), 'A4' );
		$mpdf->WriteHTML ( $this->content );
		$mpdf->Output ( $this->cached_file );
		
		$this->httpHeader ();
		readfile ( $this->cached_file );
		exit ();
	}
}
