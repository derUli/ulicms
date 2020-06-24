<?php

use UliCMS\Helpers\DataTablesHelper;

class DataTablesHelperTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        chdir(Path::resolve("ULICMS_ROOT/admin"));
    }

    protected function tearDown(): void {
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
