<?php
class Password {
	public $value = null;
	public function __construct($password) {
		$this->value = $password;
	}
	public function __toString() {
		return ! is_null ( $this->value ) ? strval ( $this->value ) : "[No Password]";
	}
}
