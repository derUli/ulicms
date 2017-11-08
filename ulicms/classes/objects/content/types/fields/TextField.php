<?php
class TextField extends CustomField {
	public function render($value = null) {
		ViewBag::set ( "field", $this );
		ViewBag::set("field_value", $value);
		return Template::executeDefaultOrOwnTemplate ( "fields/textfield.php" );
	}
}