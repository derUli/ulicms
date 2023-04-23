<?php

class PreparedStatementsTest extends \PHPUnit\Framework\TestCase {
    public function testPreparedStatements() {
        $result = Database::pQuery(
            'SELECT ? as wert1, ? as wert2, ? as wert3, ? as wert4, ? as wert5, ? as wert6',
            [
                123,
                1.85,
                'My Text',
                '2014-11-22 13:23:44.657',
                true,
                false
            ]
        );
        $dataset = Database::fetchObject($result);
        $this->assertEquals(123, $dataset->wert1);
        $this->assertEquals('1.85', $dataset->wert2);
        $this->assertEquals('My Text', $dataset->wert3);
        $this->assertEquals('2014-11-22 13:23:44.657', $dataset->wert4);
        $this->assertEquals(1, $dataset->wert5);
        $this->assertEquals(0, $dataset->wert6);
    }
}
