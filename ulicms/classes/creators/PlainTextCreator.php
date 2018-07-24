<?php
class PlainTextCreator {
	public $target_file = null;
	public $content = null;
	public $title = null;
	public function __construct() {
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
		$uid = CacheUtil::getCurrentUid ();
		$adapter = CacheUtil::getAdapter ();
		if ($adapter and $adapter->has ( $uid )) {
			$adapter->get ( $uid );
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
		
		$this->httpHeader ();
		echo $this->content;
		if ($adapter) {
			$adapter->set ( $uid, $this->content, CacheUtil::getCachePeriod () );
		}
		exit ();
	}
}
