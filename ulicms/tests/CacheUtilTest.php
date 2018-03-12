<?php
use phpFastCache\Helper\Psr16Adapter;
class CacheUtilTest extends PHPUnit_Framework_TestCase {
	private $cacheDisabledOriginal;
	private $cachePeriodOriginal;
	public function setUp() {
		$this->cacheDisabledOriginal = Settings::get ( "cache_disabled" );
		$this->cachePeriodOriginal = Settings::get ( "cache_period" );
		Settings::delete ( "cache_disabled" );
	}
	public function tearDown() {
		if ($this->cacheDisabledOriginal) {
			Settings::set ( "cache_disabled", "yes" );
		} else {
			Settings::delete ( "cache_disabled" );
		}
		Settings::set ( "cache_period", $this->cachePeriodOriginal );
	}
	public function testIsCacheEnabled() {
		Settings::delete ( "cache_disabled" );
		$this->assertTrue ( CacheUtil::isCacheEnabled () );
		
		Settings::set ( "cache_disabled", "yes" );
		$this->assertFalse ( CacheUtil::isCacheEnabled () );
		
		Settings::delete ( "cache_disabled" );
	}
	public function testIsCacheEnabledLoggedIn() {
		$_SESSION ["logged_in"] = true;
		Settings::delete ( "cache_disabled" );
		$this->assertFalse ( CacheUtil::isCacheEnabled () );
		
		Settings::set ( "cache_disabled", "yes" );
		$this->assertFalse ( CacheUtil::isCacheEnabled () );
		unset($_SESSION ["logged_in"]);
		Settings::delete ( "cache_disabled" );
	}
	public function testGetAdapter() {
		$this->assertInstanceOf ( Psr16Adapter::class, CacheUtil::getAdapter () );
	}
	public function testGetCachePeriod() {
		Settings::set ( "cache_period", 123 );
		$this->assertEquals ( 123, CacheUtil::getCachePeriod () );
		Settings::set ( "cache_period", 456 );
		$this->assertEquals ( 456, CacheUtil::getCachePeriod () );
		Settings::set ( "cache_period", 0 );
		$this->assertEquals ( 0, CacheUtil::getCachePeriod () );
	}
	public function testGetCurrentUid() {
		$_SERVER ["REQUEST_URI"] = "/my-url.html";
		$_SERVER ["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1";
		$_SESSION ["language"] = "de";
		$this->assertEquals ( "47d665610afb1110eb8d992c39815bab", CacheUtil::getCurrentUid () );
		
		$_SESSION ["language"] = "en";
		$this->assertEquals ( "f187b0bf3f93fbd42313457bfad5a644", CacheUtil::getCurrentUid () );
		
		$_SERVER ["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5";
		$this->assertEquals ( "ea6bc2d5c2086ae1db091523f6f0e197", CacheUtil::getCurrentUid () );
		
		$_SERVER ["REQUEST_URI"] = "/other-url.html?param=value";
		$this->assertEquals ( "3dfcaf39f9916a423e91dc4addfaa0ba", CacheUtil::getCurrentUid () );
	}
}