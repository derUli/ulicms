<?php
class FileGetContentsWrapperTest extends PHPUnit_Framework_TestCase {
	const EXAMPLE_URL_OK = "http://example.org/";
	const EXAMPLE_URL_INVALID = "http://www.google.de";
	const EXAMPLE_HASH = "09b9c392dc1f6e914cea287cb6be34b0";
	public function testDownloadUrlWithChecksumValid() {
		$this->assertTrue ( is_string ( file_get_contents_wrapper ( self::EXAMPLE_URL_OK, true, self::EXAMPLE_HASH ) ) );
	}
	public function testDownloadUrlWithChecksumInvalid() {
		try {
			file_get_contents_wrapper ( self::EXAMPLE_URL_INVALID, true, self::EXAMPLE_HASH );
			$this->fail ( "Expected Exception has not been raised." );
		} catch ( CorruptDownloadException $ex ) {
			$this->assertEquals ( "Download of " . self::EXAMPLE_URL_INVALID . " - Checksum validation failed", $ex->getMessage () );
		}
	}
	public function testIsURL() {
		$this->assertTrue ( is_url ( "http://example.org" ) );
		$this->assertTrue ( is_url ( "https://www.ulicms.de" ) );
		$this->assertTrue ( is_url ( "ftp://ftp.hostserver.de/pub/OpenBSD/" ) );
		$this->assertFalse ( is_url ( "/var/www/html" ) );
		$this->assertFalse ( is_url ( "C:\\xampp\\htdocs" ) );
	}
	public function testUrlExists() {
		$this->assertTrue ( url_exists ( "http://example.org" ) );
		$this->assertTrue ( url_exists ( "https://www.ulicms.de/content/images/67cc042b9ee9eb28cdc81ae7d7420d8a.png" ) );
		$this->assertFalse ( url_exists ( "http://www.gibtsnicht.de/" ) );
		$this->assertFalse ( url_exists ( "https://www.ulicms.de/gibtsnicht.html" ) );
	}

  public function testFileGetContentsCurl(){
		  $this->assertTrue ( is_string ( file_get_contents_curl( "http://example.org" ) ));
			$this->assertTrue ( is_string ( file_get_contents_curl( "http://www.gibtsnicht.de" ) ));
	}

	// TODO:
	// Tests für Downloads ohne Checksum mit Cache und ohne Cache implementieren
}
