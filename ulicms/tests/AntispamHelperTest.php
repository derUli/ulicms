<?php
class AntispamHelperTest extends PHPUnit_Framework_TestCase {
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
		throw new NotImplementedException ();
	}
}
	
