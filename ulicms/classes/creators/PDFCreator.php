<?php
$mpdf = ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "MPDF60" . DIRECTORY_SEPERATOR . "mpdf.php";
if (is_file ( $mpdf )) {
	require_once ($mpdf);
}
class PDFCreator {
	var $target_file = null;
	var $content = null;
	var $language = null;
	var $paper_format = "A4";
	public function __construct() {
		ob_start ();
		echo "<h1>" . get_title () . "</h1>";
		content ();
		$this->content = ob_get_clean ();
	}
	private function httpHeader() {
		header ( "Content-type: application/pdf" );
	}
	public function output() {
		$hasModul = containsModule ( get_requested_pagename () );
		
		if (! class_exists ( "mPDF" )) {
			echo "mPDF is not installed. Please install <a href=\"http://www.ulicms.de/mpdf_supplement.html\" target=\"_blank\">mPDF supplement</a>.";
			die ();
		}
		
		// TODO: Reimplement Caching of generated pdf files
		
		$mpdf = new mPDF ( getCurrentLanguage ( true ), 'A4' );
		$mpdf->WriteHTML ( $this->content );
		$this->httpHeader ();
		$mpdf->output ();
		exit ();
	}
}
