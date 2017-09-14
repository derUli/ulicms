<?php
class PrependSchemaTest extends PHPUnit_Framework_TestCase {
	public function testPrependSchema() {
		$this->assertEquals ( "http://www.google.de", prepend_scheme ( "www.google.de" ) );
		$this->assertEquals ( "http://www.google.de", prepend_scheme ( "http://www.google.de" ) );
		$this->assertEquals ( "https://www.google.de", prepend_scheme ( "https://www.google.de" ) );
		$this->assertEquals ( "ftp://www.google.de", prepend_scheme ( "ftp://www.google.de" ) );
		$this->assertEquals ( "ftps://www.google.de", prepend_scheme ( "ftps://www.google.de" ) );
		$this->assertEquals ( "http://www.google.de", prepend_scheme ( "www.google.de", "http://" ) );
		$this->assertEquals ( "https://www.google.de", prepend_scheme ( "www.google.de", "https://" ) );
		$this->assertEquals ( "ftp://www.google.de", prepend_scheme ( "www.google.de", "ftp://" ) );
		$this->assertEquals ( "ftps://www.google.de", prepend_scheme ( "www.google.de", "ftps://" ) );
		$this->assertEquals ( "ftp://www.google.de", prepend_scheme ( "ftp://www.google.de", "ftp://" ) );
		$this->assertEquals ( "ftps://www.google.de", prepend_scheme ( "ftps://www.google.de", "ftps://" ) );
		$this->assertEquals ( "ftps://www.google.de", prepend_scheme ( "ftps://www.google.de", "http://" ) );
		$this->assertEquals ( "http://www.google.de", prepend_scheme ( "http://www.google.de", "https://" ) );
	}
}
