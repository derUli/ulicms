<?php
class PreparedStatementTest extends PHPUnit_Framework_TestCase {
	public function testPreparedStatements() {
		$query = Database::pQuery ( "SELECT ? as wert1, ? as wert2, ? as wert3, ? as wert4", array (
				123,
				1.85,
				"My Text",
				"2014-11-22 13:23:44.657" 
		) );
		$result = Database::fetchObject ( $query );
		$this->assertEquals ( 123, $result->wert1 );
		$this->assertEquals ( "1.85", $result->wert2 );
		$this->assertEquals ( "My Text", $result->wert3 );
		$this->assertEquals ( "2014-11-22 13:23:44.657", $result->wert4 );
	}
}