<?php
class PreparedStatementTest extends PHPUnit_Framework_TestCase {
	public function testPreparedStatements() {
		$query = Database::pQuery ( "SELECT ? as wert1, ? as wert2, ? as wert3, ? as wert4, ? as wert5, ? as wert6", array (
				123,
				1.85,
				"My Text",
				"2014-11-22 13:23:44.657",
				true,
				false 
		) );
		$result = Database::fetchObject ( $query );
		$this->assertEquals ( 123, $result->wert1 );
		$this->assertEquals ( "1.85", $result->wert2 );
		$this->assertEquals ( "My Text", $result->wert3 );
		$this->assertEquals ( "2014-11-22 13:23:44.657", $result->wert4 );
		$this->assertEquals ( 1, $result->wert5 );
		$this->assertEquals ( 0, $result->wert6 );
	}
}