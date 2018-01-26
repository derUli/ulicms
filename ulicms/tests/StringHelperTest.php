<?php
class StringHelperTest extends PHPUnit_Framework_TestCase {
	private function getTestFilePath() {
		return dirname ( __FILE__ ) . "/fixtures/lines.txt";
	}
	public function testlinesFromFile() {
		$lines = StringHelper::linesFromFile ( $this->getTestFilePath (), false, false, false );
		$this->assertCount ( 9, $lines );
		$this->assertFalse ( startsWith ( $lines [2], " " ) );
		$this->assertTrue ( endsWith ( $lines [2], " " ) );
		$this->assertTrue ( startsWith ( $lines [3], " " ) );
		$this->assertTrue ( endsWith ( $lines [3], " " ) );
		$this->assertEquals ( 17, strlen ( $lines [2] ) );
		$this->assertEquals ( 23, strlen ( $lines [3] ) );
	}
	public function testlinesFromFileRemoveEmpty() {
		$lines = StringHelper::linesFromFile ( $this->getTestFilePath (), false, true, false );
		$this->assertCount ( 5, $lines );
	}
	public function testlinesFromFileRemoveComments() {
		$lines = StringHelper::linesFromFile ( $this->getTestFilePath (), false, false, true );
		$this->assertCount ( 7, $lines );
	}
	public function testlinesFromFileRemoveCommentsAndEmpty() {
		$lines = StringHelper::linesFromFile ( $this->getTestFilePath (), false, true, true );
		$this->assertCount ( 3, $lines );
	}
	public function testlinesFromFileTrim() {
		$lines = StringHelper::linesFromFile ( $this->getTestFilePath (), true, false, false );
		$this->assertCount ( 9, $lines );
		$this->assertFalse ( startsWith ( $lines [2], " " ) );
		$this->assertFalse ( endsWith ( $lines [2], " " ) );
		$this->assertFalse ( startsWith ( $lines [3], " " ) );
		$this->assertFalse ( endsWith ( $lines [3], " " ) );
		$this->assertEquals ( 16, strlen ( $lines [2] ) );
		$this->assertEquals ( 21, strlen ( $lines [3] ) );
	}
	public function testlinesFromFileTrimRemoveCommentsAndEmpty() {
		$lines = StringHelper::linesFromFile ( $this->getTestFilePath (), true, true, true );
		$this->assertCount ( 3, $lines );
		$this->assertFalse ( startsWith ( $lines [0], " " ) );
		$this->assertFalse ( endsWith ( $lines [0], " " ) );
		$this->assertFalse ( startsWith ( $lines [1], " " ) );
		$this->assertFalse ( endsWith ( $lines [1], " " ) );
		
		$this->assertEquals ( 16, strlen ( $lines [0] ) );
		$this->assertEquals ( 21, strlen ( $lines [1] ) );
	}
	public function testLinesFromFileNotFound() {
		$lines = StringHelper::linesFromFile ( "path/this-is-not-a-file", true, true, true );
		$this->assertNull ( $lines );
	}
}