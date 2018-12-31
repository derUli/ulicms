<?php
use OddAndEven;
class OddAndEvenTest extends \PHPUnit\Framework\TestCase {
	public function testIsEvenWithIntegerExpectTrue() {
		$this->assertTrue ( OddAndEven\is_even ( 2 ) );
		$this->assertTrue ( OddAndEven\is_even ( 56 ) );
		$this->assertTrue ( OddAndEven\is_even ( 100 ) );
	}
	public function testIsEvenWithIntegerExpectFalse() {
		$this->assertFalse ( OddAndEven\is_even ( 1 ) );
		$this->assertFalse ( OddAndEven\is_even ( 13 ) );
	}
	public function testIsOddWithIntegerExpectTrue() {
		$this->assertTrue ( OddAndEven\is_odd ( 1 ) );
		$this->assertTrue ( OddAndEven\is_odd ( 13 ) );
		$this->assertTrue ( OddAndEven\is_odd ( pi () ) );
	}
	public function testIsOddWithIntegerExpectFalse() { //
		$this->assertFalse ( OddAndEven\is_odd ( 2 ) );
		$this->assertFalse ( OddAndEven\is_odd ( 56 ) );
		$this->assertFalse ( OddAndEven\is_odd ( 100 ) );
	}
	public function testIsEvenWithFloatExpectTrue() {
		$this->assertTrue ( OddAndEven\is_even ( 1.99 ) );
		$this->assertTrue ( OddAndEven\is_even ( 5.95 ) );
		$this->assertTrue ( OddAndEven\is_even ( 6.10 ) );
	}
	public function testIsEvenWithFloatExpectFalse() {
		$this->assertFalse ( OddAndEven\is_even ( 0.99 ) );
		$this->assertFalse ( OddAndEven\is_even ( 8.99 ) );
		$this->assertFalse ( OddAndEven\is_even ( 18.95 ) );
	}
	public function testIsOddWithFloatExpectTrue() {
		$this->assertTrue ( OddAndEven\is_odd ( 1.12 ) );
		$this->assertTrue ( OddAndEven\is_odd ( 11.05 ) );
		$this->assertTrue ( OddAndEven\is_odd ( pi () ) );
	}
	public function testIsOddWithFloatExpectFalse() { //
		$this->assertFalse ( OddAndEven\is_odd ( 2.10 ) );
		$this->assertFalse ( OddAndEven\is_odd ( 56.42 ) );
		$this->assertFalse ( OddAndEven\is_odd ( 100 ) );
	}
	public function testIsEvenWithInvalidArgument() {
		try {
			OddAndEven\is_odd ( "not a number" );
			$this->fail ( "Expected exception not thrown" );
		} catch ( Exception $e ) {
			$this->assertInstanceOf ( \InvalidArgumentException::class, $e );
		}
	}
	public function testIsOddWithInvalidArgument() {
		try {
			OddAndEven\is_even ( "not a number" );
			$this->fail ( "Expected exception not thrown" );
		} catch ( Exception $e ) {
			$this->assertInstanceOf ( \InvalidArgumentException::class, $e );
		}
	}
}