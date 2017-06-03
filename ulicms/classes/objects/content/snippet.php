<?php
class Snippet extends Page {
	public $type = "snippet";
	public $menu = "none";
	public $hidden = true;
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->type = "snippet";
		$this->mehu = "none";
		$this->hidden = true;
	}
}