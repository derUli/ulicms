<?php

class CsvWriterTest extends \PHPUnit\Framework\TestCase {

    public function testGetCSV() {

        $this->assertEquals('foo,123,"29,95"', getCSV(array("foo", 123, 29.95)));
    }

}
