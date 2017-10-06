<?php
class Authorization {
	public $type = null;
	public $user = null;
	public $password = null;
	public function __construct($header = null) {
		if ($header) {
			$this->fromHeader ( $header );
		}
	}
	public function fromHeader($header = null) {
	}
	public function __toString() {
	}
}