<?php
class Content_List extends Page {
	public $listData = null;
	protected $type = "list";
	public function __construct() {
		parent::__construct ();
		$this->listData = new List_Data ();
	}
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->listData = new List_Data ( $this->id );
	}
}

