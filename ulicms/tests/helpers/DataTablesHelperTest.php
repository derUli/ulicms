<?php

class DataTablesHelperTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        chdir(Path::resolve("ULICMS_ROOT/admin"));
    }

    public function tearDown() {
        chdir(Path::resolve("ULICMS_ROOT"));
    }

    public function testGetLanguageFileURLExists() {
        $this->assertEquals("scripts/datatables/lang/de.lang", DataTablesHelper::getLanguageFileURL("de"));
        $this->assertEquals("scripts/datatables/lang/en.lang", DataTablesHelper::getLanguageFileURL("en"));
    }

    public function testGetlanguageFileNotExists() {
        $this->assertEquals("scripts/datatables/lang/en.lang", DataTablesHelper::getLanguageFileURL("cn"));
    }

}
