<?php
class Content_List extends Content {
	public $listData = null;
	private $type = "list";
	public function __construct() {
		parent::__construct ();
		$this->listData = new List_Data ();
	}
	private function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->listData = new List_Data ( $this->id );
	}
}

