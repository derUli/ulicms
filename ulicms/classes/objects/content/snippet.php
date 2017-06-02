<?php
class Snippet extends Page {
	public $type = "snippet";
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->type = "snippet";
	}
}