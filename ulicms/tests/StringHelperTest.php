<?php
class StringHelperTest extends PHPUnit_Framework_TestCase {
	private function getTestText() {
		$file = dirname ( __FILE__ ) . "/fixtures/linesFromString.txt";
		return file_get_contents ( $file );
	}
	public function testLinesFromString() {
		$str = $this->getTestText ();
		$lines = StringHelper::linesFromString ( $str, false, false, false );
		$this->assertCount ( 9, $lines );
		$this->assertFalse ( startsWith ( $lines [2], " " ) );
		$this->assertTrue ( endsWith ( $lines [2], " " ) );
		$this->assertTrue ( startsWith ( $lines [3], " " ) );
		$this->assertTrue ( endsWith ( $lines [3], " " ) );
		$this->assertEquals ( 17, strlen ( $lines [2] ) );
		$this->assertEquals ( 23, strlen ( $lines [3] ) );
	}
	public function testLinesFromStringRemoveEmpty() {
		$str = $this->getTestText ();
		$lines = StringHelper::linesFromString ( $str, false, true, false );
		$this->assertCount ( 5, $lines );
	}
	public function testLinesFromStringRemoveComments() {
		$str = $this->getTestText ();
		$lines = StringHelper::linesFromString ( $str, false, false, true );
		$this->assertCount ( 7, $lines );
	}
	public function testLinesFromStringRemoveCommentsAndEmpty() {
		$str = $this->getTestText ();
		$lines = StringHelper::linesFromString ( $str, false, true, true );
		$this->assertCount ( 3, $lines );
	}
	public function testLinesFromStringTrim() {
		$str = $this->getTestText ();
		$lines = StringHelper::linesFromString ( $str, true, false, false );
		$this->assertCount ( 9, $lines );
		$this->assertFalse ( startsWith ( $lines [2], " " ) );
		$this->assertFalse ( endsWith ( $lines [2], " " ) );
		$this->assertFalse ( startsWith ( $lines [3], " " ) );
		$this->assertFalse ( endsWith ( $lines [3], " " ) );
		$this->assertEquals ( 16, strlen ( $lines [2] ) );
		$this->assertEquals ( 21, strlen ( $lines [3] ) );
	}
	public function testLinesFromStringTrimRemoveCommentsAndEmpty() {
		$str = $this->getTestText ();
		$lines = StringHelper::linesFromString ( $str, true, true, true );
		$this->assertCount ( 3, $lines );
		$this->assertFalse ( startsWith ( $lines [0], " " ) );
		$this->assertFalse ( endsWith ( $lines [0], " " ) );
		$this->assertFalse ( startsWith ( $lines [1], " " ) );
		$this->assertFalse ( endsWith ( $lines [1], " " ) );
		
		$this->assertEquals ( 16, strlen ( $lines [0] ) );
		$this->assertEquals ( 21, strlen ( $lines [1] ) );
	}
}