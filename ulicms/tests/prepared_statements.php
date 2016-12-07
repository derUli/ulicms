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
}
