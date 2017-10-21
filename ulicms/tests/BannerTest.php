<?php
class BannerTest extends PHPUnit_Framework_TestCase {
	const HTML_TEXT1 = "My first Banner HTML";
	const HTML_TEXT2 = "My second Banner HTML";
	public function setUp() {
		Database::pQuery ( "DELETE FROM `{prefix}banner` where html in (? , ?)", array (
				self::HTML_TEXT1,
				self::HTML_TEXT1 
		), true );
	}
	public function tearDown() {
		$this->setUp ();
	}
	public function testHTMLBannerWithoutLanguage() {
		$banner = new Banner ();
		$banner->setType ( "html" );
		$banner->html = self::HTML_TEXT1;
		$banner->save ();
		$this->assertNotNull ( $banner->id );
		$id = intval ( $banner->id );
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "html", $banner->getType () );
		$this->assertEquals ( self::HTML_TEXT1, $banner->html );
		$this->assertNull ( $banner->language );
		$banner->html = self::HTML_TEXT2;
		$banner->save ();
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "html", $banner->getType () );
		$this->assertEquals ( self::HTML_TEXT2, $banner->html );
		$this->assertNull ( $banner->language );
		$banner->delete ();
		$banner = new Banner ();
		$this->assertNull ( $banner->id );
	}
	public function testHTMLBannerWithLanguage() {
		$banner = new Banner ();
		$banner->setType ( "html" );
		$banner->html = self::HTML_TEXT1;
		$banner->language = "de";
		$banner->save ();
		$this->assertNotNull ( $banner->id );
		$id = intval ( $banner->id );
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "html", $banner->getType () );
		$this->assertEquals ( self::HTML_TEXT1, $banner->html );
		$this->assertEquals ( "de", $banner->language );
		$banner->html = self::HTML_TEXT2;
		$banner->language = "en";
		$banner->save ();
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "html", $banner->getType () );
		$this->assertEquals ( self::HTML_TEXT2, $banner->html );
		
		$this->assertEquals ( "en", $banner->language );
		$banner->delete ();
		$banner = new Banner ();
		$this->assertNull ( $banner->id );
	}
	// TODO: Tests für Gif-Banner anlegen mit Sprache und ohne Sprache implementieren
}