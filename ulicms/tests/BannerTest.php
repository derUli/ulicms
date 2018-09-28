<?php
class BannerTest extends \PHPUnit\Framework\TestCase {
	const HTML_TEXT1 = "My first Banner HTML";
	const HTML_TEXT2 = "My second Banner HTML";
	const NAME_TEXT1 = "My first Gif Banner";
	const NAME_TEXT2 = "My second Gif Banner";
	const IMAGE_URL_TEXT1 = "http://firma.de/bild.gif";
	const IMAGE_URL_TEXT2 = "http://firma.de/bild2.gif";
	const LINK_URL_TEXT1 = "http://www.google.de";
	const LINK_URL_TEXT2 = "http://www.yahoo.com";
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
	public function testGifBannerWithoutLanguage() {
		$banner = new Banner ();
		$banner->setType ( "gif" );
		$banner->name = self::NAME_TEXT1;
		$banner->image_url = self::IMAGE_URL_TEXT1;
		$banner->link_url = self::LINK_URL_TEXT1;
		$banner->save ();
		$this->assertNotNull ( $banner->id );
		$id = intval ( $banner->id );
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "gif", $banner->getType () );
		$this->assertEquals ( self::NAME_TEXT1, $banner->name );
		$this->assertEquals ( self::IMAGE_URL_TEXT1, $banner->image_url );
		$this->assertEquals ( self::LINK_URL_TEXT1, $banner->link_url );
		$this->assertNull ( $banner->language );
		$banner->name = self::NAME_TEXT2;
		$banner->image_url = self::IMAGE_URL_TEXT2;
		$banner->link_url = self::LINK_URL_TEXT2;
		$banner->save ();
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "gif", $banner->getType () );
		$this->assertEquals ( self::NAME_TEXT2, $banner->name );
		$this->assertEquals ( self::IMAGE_URL_TEXT2, $banner->image_url );
		$this->assertEquals ( self::LINK_URL_TEXT2, $banner->link_url );
		$this->assertNull ( $banner->language );
		$banner->delete ();
		$banner = new Banner ();
		$this->assertNull ( $banner->id );
	}
	public function testGifBannerWithLanguage() {
		$banner = new Banner ();
		$banner->setType ( "gif" );
		$banner->language = "de";
		$banner->name = self::NAME_TEXT1;
		$banner->image_url = self::IMAGE_URL_TEXT1;
		$banner->link_url = self::LINK_URL_TEXT1;
		$banner->save ();
		$this->assertNotNull ( $banner->id );
		$id = intval ( $banner->id );
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "gif", $banner->getType () );
		$this->assertEquals ( self::NAME_TEXT1, $banner->name );
		$this->assertEquals ( self::IMAGE_URL_TEXT1, $banner->image_url );
		$this->assertEquals ( self::LINK_URL_TEXT1, $banner->link_url );
		$this->assertEquals ( "de", $banner->language );
		$banner->language = "en";
		$banner->name = self::NAME_TEXT2;
		$banner->image_url = self::IMAGE_URL_TEXT2;
		$banner->link_url = self::LINK_URL_TEXT2;
		$banner->save ();
		$banner = new Banner ( $id );
		$this->assertNotNull ( $banner->id );
		$this->assertEquals ( "gif", $banner->getType () );
		$this->assertEquals ( self::NAME_TEXT2, $banner->name );
		$this->assertEquals ( self::IMAGE_URL_TEXT2, $banner->image_url );
		$this->assertEquals ( self::LINK_URL_TEXT2, $banner->link_url );
		$this->assertEquals ( "en", $banner->language );
		$banner->delete ();
		$banner = new Banner ();
		$this->assertNull ( $banner->id );
	}
}