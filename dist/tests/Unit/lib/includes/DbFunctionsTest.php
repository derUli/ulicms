<?php

use App\Exceptions\SqlException;

// test for legacy database methods
class DbFunctionsTest extends \PHPUnit\Framework\TestCase {
    public function testTbName(): void {
        $tableName = tbname('random_table');

        $this->assertStringEndsWith('random_table', $tableName);
        $this->assertStringStartsNotWith('random_table', $tableName);
    }

    public function testDbError(): void {
        try {
            Database::selectAll('gibts_nicht');
        } catch (SqlException $e) {
        } finally {
            $this->assertStringEndsWith(
                "gibts_nicht' doesn't exist",
                Database::getLastError()
            );
        }
    }
}
