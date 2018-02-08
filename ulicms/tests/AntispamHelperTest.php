<?php
class AntispamHelperTest extends PHPUnit_Framework_TestCase {
	private $initialCountryBlacklist;
	public function setUp() {
		$this->initialCountryBlacklist = Settings::get ( "country_blacklist" );
		Settings::set ( "spamfilter_enabled", "yes" );
	}
	public function tearDown() {
		Settings::set ( "country_blacklist", $this->initialCountryBlacklist );
		unset ( $_SERVER ["REMOTE_ADDR"] );
		Settings::set ( "spamfilter_enabled", "yes" );
	}
	public function testIsChinese() {
		// Only Latin
		$this->assertFalse ( AntiSpamHelper::isChinese ( "Deutsche Büchstäben" ) );
		// Only chinese
		$this->assertTrue ( AntiSpamHelper::isChinese ( "这只是一个简单的文字" ) );
		// Mixed Latin and Chinese
		$this->assertTrue ( AntiSpamHelper::isChinese ( "Deutsche 这只是一个简单的文字
 Büchstäbem" ) );
		// korean
		$this->assertFalse ( AntiSpamHelper::isChinese ( "이것은 단순한 텍스트입니다." ) );
		// Russian
		$this->assertFalse ( AntiSpamHelper::isChinese ( "Это просто простой текст" ) );
	}
	public function testIsCyrillic() {
		// Only Latin
		$this->assertFalse ( AntiSpamHelper::isCyrillic ( "Deutsche Büchstäben" ) );
		// Only cyrillic
		$this->assertTrue ( AntiSpamHelper::isCyrillic ( "Это просто простой текст" ) );
		// Mixed Latin and Cyrillic
		$this->assertTrue ( AntiSpamHelper::isCyrillic ( "Deutsche Это просто простой текст
 Büchstäbem" ) );
		// korean
		$this->assertFalse ( AntiSpamHelper::isCyrillic ( "이것은 단순한 텍스트입니다." ) );
		// Ukrainian
		$this->assertTrue ( AntiSpamHelper::isCyrillic ( "Це просто текст" ) );
	}
	public function testCheckForSpamhaus() {
		// ip of ulicms.de webserver - clean
		$this->assertFalse ( AntiSpamHelper::checkForSpamhaus ( "85.13.143.60" ) );
		
		// known spam ip
		$this->assertTrue ( AntiSpamHelper::checkForSpamhaus ( "185.36.102.114" ) );
	}
	
	// TODO: Implement test for isCountryBlocked
	public function testIsCountryBlocked() {
		Settings::set ( "country_blacklist", "vn,jp, at,tr" );
		
		// Germany
		$_SERVER ["REMOTE_ADDR"] = "178.254.29.67";
		$this->assertFalse ( AntiSpamHelper::isCountryBlocked () );
		
		// Italy
		$_SERVER ["REMOTE_ADDR"] = "40.84.199.233";
		$this->assertFalse ( AntiSpamHelper::isCountryBlocked () );
		
		// United Kingdom
		$_SERVER ["REMOTE_ADDR"] = "52.222.250.185";
		$this->assertFalse ( AntiSpamHelper::isCountryBlocked () );
		
		// Vietnam
		$_SERVER ["REMOTE_ADDR"] = "123.30.54.106";
		$this->assertTrue ( AntiSpamHelper::isCountryBlocked () );
		
		// Japan
		$_SERVER ["REMOTE_ADDR"] = "183.79.23.196";
		$this->assertTrue ( AntiSpamHelper::isCountryBlocked () );
		
		// Austria
		$_SERVER ["REMOTE_ADDR"] = "194.116.243.20";
		$this->assertTrue ( AntiSpamHelper::isCountryBlocked () );
		
		// Turkey
		$_SERVER ["REMOTE_ADDR"] = "88.255.55.110";
		$this->assertTrue ( AntiSpamHelper::isCountryBlocked () );
	}
	public function testContainsBadWords() {
		$this->assertNull ( AntiSpamHelper::containsBadwords ( "This is a clean text without spammy words" ) );
		$this->assertEquals ( "viagra", AntiSpamHelper::containsBadwords ( "Buy cheap Viagra pills." ) );
	}
	public function testIsSpamFilterEnabled() {
		Settings::delete ( "spamfilter_enabled", "yes" );
		$this->assertFalse ( AntiSpamHelper::isSpamFilterEnabled () );
		Settings::set ( "spamfilter_enabled", "yes" );
		$this->assertTrue ( AntiSpamHelper::isSpamFilterEnabled () );
	}
}
	