<?php
class HtmlField extends CustomField {
	public function render($value = null) {
		if (! isset ( $this->htmlAttributes ["class"] )) {
			$this->htmlAttributes ["class"] = get_html_editor ();
		}
		if (get_html_editor () == "codemirror") {
			$this->htmlAttributes ["data-mimetype"] = "text/html";
		}
		ViewBag::set ( "field", $this );
		ViewBag::set ( "field_value", $value );
		ViewBag::set ( "field_name", ! is_null ( $this->$contentType ) ? $this->$contentType . "_" . $this->name : $this->name );
		
		return Template::executeDefaultOrOwnTemplate ( "fields/htmlfield.php" );
	}
}
