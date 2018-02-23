<?php
use UliCMS\HTML\Input as Input;
class HTMLInpuit extends PHPUnit_Framework_TestCase {
	public function testTextBox() {
		$this->assertEquals ( '<input type="text" name="my_field" value="Hello World" required="required">', Input::TextBox ( "my_field", "Hello World", "text", array (
				"required" => "required" 
		) ) );
	}
	public function testPassword() {
		$this->assertEquals ( '<input type="password" name="my_field" value="Hello World" required="required">', Input::Password ( "my_field", "Hello World", "text", array (
				"required" => "required" 
		) ) );
	}
	public function testHidden() {
		$this->assertEquals ( '<input type="hidden" name="my_field" value="Hello World" required="required">', Input::Hidden ( "my_field", "Hello World", "text", array (
				"required" => "required" 
		) ) );
	}
	public function testCheckbox() {
		$this->assertEquals ( '<input type="checkbox" name="my_field" value="1">', Input::CheckBox ( "my_field" ) );
		$this->assertEquals ( '<input type="checkbox" name="my_field" value="1" checked="checked">', Input::CheckBox ( "my_field", true ) );
	}
}