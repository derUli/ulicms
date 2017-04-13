<?php
class Node extends Link {
	protected $type = "node";
	protected $redirection = "#";
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->redirection = "#";
	}
}