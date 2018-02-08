<?php
class CSVCreator {
	var $target_file = null;
	var $content = null;
	var $title = null;
	public function __construct() {
		$this->title = get_title ();
		ob_start ();
		content ();
		$this->content = ob_get_clean ();
	}
	private function httpHeader() {
		header ( "Content-type: text/csv; charset=UTF-8" );
	}
	public function output() {
		$uid = CacheUtil::getCurrentUid ();
		$adapter = CacheUtil::getAdapter ();
		if ($adapter and $adapter->has ( $uid )) {
			$adapter->get ( $uid );
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
		
		$this->httpHeader ();
		echo $csv_string;
		if ($adapter) {
			$adapter->set ( $uid, $csv_string, CacheUtil::getCachePeriod () );
		}
		exit ();
	}
}
