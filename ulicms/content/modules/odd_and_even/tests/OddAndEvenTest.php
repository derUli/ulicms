<?php
use OddAndEven;
class OddAndEvenTest extends \PHPUnit\Framework\TestCase {
	public function testIsEvenWithIntegerExpectTrue() {
		$this->assertTrue ( OddAndEven\isEven ( 2 ) );
		$this->assertTrue ( OddAndEven\isEven ( 56 ) );
		$this->assertTrue ( OddAndEven\isEven ( 100 ) );
	}
	public function testIsEvenWithIntegerExpectFalse() {
		$this->assertFalse ( OddAndEven\isEven ( 1 ) );
		$this->assertFalse ( OddAndEven\isEven ( 13 ) );
	}
	public function testIsOddWithIntegerExpectTrue() {
		$this->assertTrue ( OddAndEven\isOdd ( 1 ) );
		$this->assertTrue ( OddAndEven\isOdd ( 13 ) );
		$this->assertTrue ( OddAndEven\isOdd ( pi () ) );
	}
	public function testIsOddWithIntegerExpectFalse() { //
		$this->assertFalse ( OddAndEven\isOdd ( 2 ) );
		$this->assertFalse ( OddAndEven\isOdd ( 56 ) );
		$this->assertFalse ( OddAndEven\isOdd ( 100 ) );
	}
	public function testIsEvenWithFloatExpectTrue() {
		$this->assertTrue ( OddAndEven\isEven ( 1.99 ) );
		$this->assertTrue ( OddAndEven\isEven ( 5.95 ) );
		$this->assertTrue ( OddAndEven\isEven ( 6.10 ) );
	}
	public function testIsEvenWithFloatExpectFalse() {
		$this->assertFalse ( OddAndEven\isEven ( 0.99 ) );
		$this->assertFalse ( OddAndEven\isEven ( 8.99 ) );
		$this->assertFalse ( OddAndEven\isEven ( 18.95 ) );
	}
	public function testIsOddWithFloatExpectTrue() {
		$this->assertTrue ( OddAndEven\isOdd ( 1.12 ) );
		$this->assertTrue ( OddAndEven\isOdd ( 11.05 ) );
		$this->assertTrue ( OddAndEven\isOdd ( pi () ) );
	}
	public function testIsOddWithFloatExpectFalse() { //
		$this->assertFalse ( OddAndEven\isOdd ( 2.10 ) );
		$this->assertFalse ( OddAndEven\isOdd ( 56.42 ) );
		$this->assertFalse ( OddAndEven\isOdd ( 100 ) );
	}
	public function testIsEvenWithInvalidArgument() {
		try {
			OddAndEven\isOdd ( "not a number" );
			$this->fail ( "Expected exception not thrown" );
		} catch ( Exception $e ) {
			$this->assertInstanceOf ( \InvalidArgumentException::class, $e );
		}
	}
	public function testIsOddWithInvalidArgument() {
		try {
			OddAndEven\isEven ( "not a number" );
			$this->fail ( "Expected exception not thrown" );
		} catch ( Exception $e ) {
			$this->assertInstanceOf ( \InvalidArgumentException::class, $e );
		}
	}
}
