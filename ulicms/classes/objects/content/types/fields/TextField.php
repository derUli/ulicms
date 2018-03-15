<?php
class TextField extends CustomField {
	public function render($value = null) {
		ViewBag::set ( "field", $this );
		ViewBag::set ( "field_value", $value );
		ViewBag::set ( "field_name", ! is_null ( $this->contentType ) ? $this->contentType . "_" . $this->name : $this->name );
		
		return Template::executeDefaultOrOwnTemplate ( "fields/textfield.php" );
	}
}