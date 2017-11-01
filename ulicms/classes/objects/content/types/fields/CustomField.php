<?php
class CustomField {
	public $name;
	public $title;
	public $required = false;
	public $helpText;
	public $defaultValue = "";
	public $htmlAttributes = array ();
	public function render() {
		throw new NotImplementedException ();
	}
}