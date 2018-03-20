<?php
class SelectField extends CustomField {
	public $options = array ();
	public $translateOptions = true;
	public function render($value = null) {
		ViewBag::set ( "field", $this );
		ViewBag::set ( "field_value", $value );
		ViewBag::set ( "field_options", $this->options );
		ViewBag::set ( "field_name", ! is_null ( $this->contentType ) ? $this->contentType . "_" . $this->name : $this->name );
		
		return Template::executeDefaultOrOwnTemplate ( "fields/selectfield.php" );
	}
}
class MultiSelectField extends CustomField {
	public $options = array ();
	public $translateOptions = true;
	public function render($value = null) {
		if (! isset ( $this->htmlAttributes ["multiple"] )) {
			$this->htmlAttributes ["multiple"] = "";
		}
		ViewBag::set ( "field", $this );
		ViewBag::set ( "field_value", $value );
		ViewBag::set ( "field_options", $this->options );
		ViewBag::set ( "field_name", ! is_null ( $this->contentType ) ? $this->contentType . "_" . $this->name : $this->name );
		
		return Template::executeDefaultOrOwnTemplate ( "fields/multiselect.php" );
	}
}