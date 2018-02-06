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
	// TODO:
	// Tests für Downloads ohne Checksum mit Cache und ohne Cache implementieren
}