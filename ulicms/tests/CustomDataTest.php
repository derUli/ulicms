<?php
include_once ULICMS_ROOT . "/templating.php";
class CustomDataTest extends PHPUnit_Framework_TestCase {
	public function tearDown() {
		Settings::set ( "my_value", null );
		unset ( $_GET ["seite"] );
	}
	public function testGetCustomDataOrSetting() {
		$_GET ["seite"] = "welcome";
		Settings::set ( "my_value", "text1" );
		$this->assertEquals ( "text1", CustomData::getCustomDataOrSetting ( "my_value" ) );
		CustomData::set ( "my_value", "text2" );
		$this->assertEquals ( "text1", CustomData::getCustomDataOrSetting ( "my_value" ) );
		CustomData::set ( "my_value", null );
		$this->assertEquals ( "text1", CustomData::getCustomDataOrSetting ( "my_value" ) );
	}
}