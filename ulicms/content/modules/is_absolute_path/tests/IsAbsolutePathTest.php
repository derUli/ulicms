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
	public function testIsAbsolutePathWindows() {
		$this->assertTrue ( is_absolute_path ( "c:\\" ) );
		$this->assertTrue ( is_absolute_path ( "c:\\windows" ) );
		$this->assertTrue ( is_absolute_path ( "c:\\windows\\notepad.exe" ) );
		$this->assertTrue ( is_absolute_path ( "Z:\\data\\document.docx" ) );
		$this->assertTrue ( is_absolute_path ( "y:\\data\\document.docx" ) );
		$this->assertFalse ( is_absolute_path ( "test.html" ) );
		$this->assertFalse ( is_absolute_path ( ".\\test.html" ) );
		$this->assertFalse ( is_absolute_path ( "..\\test.html" ) );
		$this->assertFalse ( is_absolute_path ( "..\\test\\test.html" ) );
		$this->assertFalse ( is_absolute_path ( "mein\\test.html" ) );
	}
}