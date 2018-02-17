<?php
class SettingsTest extends PHPUnit_Framework_TestCase {
	public function testSettingsOld() {
		deleteconfig ( "example_setting" );
		$this->assertEquals ( false, getconfig ( "example_setting" ) );
		
		initconfig ( "example_setting", "hello" );
		$this->assertEquals ( "hello", getconfig ( "example_setting" ) );
		
		initconfig ( "example_setting", "bye" );
		$this->assertEquals ( "hello", getconfig ( "example_setting" ) );
		
		setconfig ( "example_setting", "bye" );
		$this->assertEquals ( "bye", getconfig ( "example_setting" ) );
		
		deleteconfig ( "example_setting" );
		$this->assertEquals ( false, getconfig ( "example_setting" ) );
	}
	public function testSettingsNew() {
		Settings::delete ( "example_setting" );
		$this->assertEquals ( false, Settings::get ( "example_setting" ) );
		
		Settings::register ( "example_setting", "hello" );
		$this->assertEquals ( "hello", Settings::get ( "example_setting" ) );
		
		Settings::register ( "example_setting", "bye" );
		$this->assertEquals ( "hello", Settings::get ( "example_setting" ) );
		
		Settings::set ( "example_setting", "bye" );
		$this->assertEquals ( "bye", Settings::get ( "example_setting" ) );
		
		Settings::delete ( "example_setting" );
		$this->assertEquals ( false, Settings::get ( "example_setting" ) );
	}
	public function testMappingStringToArray() {
		$mappingString = "company.de => de\r\n" . "#This is a comment => This should be ignored\r\n" . "company.co.uk => en \r\n" . "company.fr=>fr";
		$mapped = Settings::mappingStringToArray ( $mappingString );
		$this->assertEquals ( 3, count ( $mapped ) );
		$this->assertEquals ( "de", $mapped ["company.de"] );
		$this->assertEquals ( "en", $mapped ["company.co.uk"] );
		$this->assertEquals ( "fr", $mapped ["company.fr"] );
		$this->assertFalse ( isset ( $mapped ["#This is a comment"] ) );
	}
}
