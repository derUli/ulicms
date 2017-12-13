<?php
class AntispamHelperTest extends PHPUnit_Framework_TestCase {
	private $initialCountryBlacklist;
	public function setUp() {
		$this->initialCountryBlacklist = Settings::get ( "country_blacklist" );
	}
	public function tearDown() {
		Settings::set ( "country_blacklist", $this->initialCountryBlacklist );
		unset ( $_SERVER ["REMOTE_ADDR"] );
	}
	public function testIsChinese() {
		// Only Latin
		$this->assertFalse ( AntispamHelper::isChinese ( "Deutsche Büchstäben" ) );
		// Only chinese
		$this->assertTrue ( AntispamHelper::isChinese ( "这只是一个简单的文字" ) );
		// Mixed Latin and Chinese
		$this->assertTrue ( AntispamHelper::isChinese ( "Deutsche 这只是一个简单的文字
 Büchstäbem" ) );
		// korean
		$this->assertFalse ( AntispamHelper::isChinese ( "이것은 단순한 텍스트입니다." ) );
		// Russian
		$this->assertFalse ( AntispamHelper::isChinese ( "Это просто простой текст" ) );
	}
	public function testIsCyrillic() {
		// Only Latin
		$this->assertFalse ( AntispamHelper::isCyrillic ( "Deutsche Büchstäben" ) );
		// Only cyrillic
		$this->assertTrue ( AntispamHelper::isCyrillic ( "Это просто простой текст" ) );
		// Mixed Latin and Cyrillic
		$this->assertTrue ( AntispamHelper::isCyrillic ( "Deutsche Это просто простой текст
 Büchstäbem" ) );
		// korean
		$this->assertFalse ( AntispamHelper::isCyrillic ( "이것은 단순한 텍스트입니다." ) );
		// Ukrainian
		$this->assertTrue ( AntispamHelper::isCyrillic ( "Це просто текст" ) );
	}
	public function testCheckForSpamhaus() {
		$this->assertFalse ( AntispamHelper::checkForSpamhaus ( "85.13.143.60" ) );
	}
	
	// TODO: Implement test for isCountryBlocked
	public function testIsCountryBlocked() {
		Settings::set ( "country_blacklist", "vn,jp, at" );
		
		// Germany
		$_SERVER ["REMOTE_ADDR"] = "178.254.29.67";
		$this->assertFalse ( AntispamHelper::isCountryBlocked () );
		
		// Italy
		$_SERVER ["REMOTE_ADDR"] = "40.84.199.233";
		$this->assertFalse ( AntispamHelper::isCountryBlocked () );
		
		// United Kingdom
		$_SERVER ["REMOTE_ADDR"] = "52.222.250.185";
		$this->assertFalse ( AntispamHelper::isCountryBlocked () );
		
		// Vietnam
		$_SERVER ["REMOTE_ADDR"] = "123.30.54.106";
		$this->assertTrue ( AntispamHelper::isCountryBlocked () );
		
		// Japan
		$_SERVER ["REMOTE_ADDR"] = "183.79.23.196";
		$this->assertTrue ( AntispamHelper::isCountryBlocked () );
		
		// Austria
		$_SERVER ["REMOTE_ADDR"] = "194.116.243.20";
		$this->assertTrue ( AntispamHelper::isCountryBlocked () );
	}
}
	