<?php
class Node extends Link {
	function __construct() {
		parent::__construct ();
		$this->type = "node";
		$this->redirection = "#";
	}
}