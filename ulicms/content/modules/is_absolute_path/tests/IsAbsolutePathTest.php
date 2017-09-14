<?php
class isAbsolutePathTest extends PHPUnit_Framework_TestCase {
	public function testIsAbsolutePathUnix() {
		$this->assertTrue ( is_absolute_path ( "/" ) );
		$this->assertTrue ( is_absolute_path ( "/var/www/html/index.html" ) );
		$this->assertTrue ( is_absolute_path ( "/var/www/html/index" ) );
		$this->assertTrue ( is_absolute_path ( "/var/www/html/index/" ) );
		$this->assertFalse ( is_absolute_path ( "test.html" ) );
		$this->assertFalse ( is_absolute_path ( "./test.html" ) );
		$this->assertFalse ( is_absolute_path ( "../test.html" ) );
		$this->assertFalse ( is_absolute_path ( "../test/test.html" ) );
		$this->assertFalse ( is_absolute_path ( "mein/test.html" ) );
	}
	// @TODO: Implement Test for windows
}