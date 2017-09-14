<?php
class PositiveNegativeTest extends PHPUnit_Framework_TestCase {
	public function testPositive() {
		$this->assertTrue ( is_positive ( 123 ) );
		$this->assertTrue ( is_positive ( 5.12 ) );
		$this->assertTrue ( is_positive ( 0.123 ) );
		$this->assertFalse ( is_positive ( 0 ) );
		$this->assertFalse ( is_positive ( - 0.12 ) );
		$this->assertTrue ( is_positive ( 0, true ) );
		$this->assertFalse ( is_positive ( 0, false ) );
		$this->assertFalse ( is_positive ( - 1 ) );
		$this->assertFalse ( is_positive ( - 123 ) );
	}
	public function testNegative() {
		$this->assertTrue ( is_negative ( - 1 ) );
		$this->assertTrue ( is_negative ( - 0.12 ) );
		$this->assertTrue ( is_negative ( - 123 ) );
		$this->assertFalse ( is_negative ( 0 ) );
		$this->assertFalse ( is_negative ( 123 ) );
	}
}