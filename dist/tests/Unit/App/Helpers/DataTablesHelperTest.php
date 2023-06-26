<?php

use App\Helpers\DataTablesHelper;

class DataTablesHelperTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        chdir(\App\Utils\Path::resolve('ULICMS_ROOT/admin'));
    }

    protected function tearDown(): void {
        chdir(\App\Utils\Path::resolve('ULICMS_ROOT'));
    }

    public function testGetLanguageFileURLExists(): void {
        $this->assertEquals('scripts/datatables/lang/de.lang', DataTablesHelper::getLanguageFileURL('de'));
        $this->assertEquals('scripts/datatables/lang/en.lang', DataTablesHelper::getLanguageFileURL('en'));
    }

    public function testGetlanguageFileNotExists(): void {
        $this->assertEquals('scripts/datatables/lang/en.lang', DataTablesHelper::getLanguageFileURL('cn'));
    }
}
