<?php

class BackendHelperTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        include_once getLanguageFilePath("en");
    }

    public function testFormatDatasetCount0()
    {
        ob_start();
        BackendHelper::formatDatasetCount(0);
        $text = ob_get_clean();
        $this->assertEquals("0 datasets found.", $text);
    }

    public function testFormatDatasetCount1()
    {
        ob_start();
        BackendHelper::formatDatasetCount(1);
        $text = ob_get_clean();
        $this->assertEquals("One dataset found.", $text);
    }

    public function testFormatDatasetCount7()
    {
        ob_start();
        BackendHelper::formatDatasetCount(7);
        $text = ob_get_clean();
        $this->assertEquals("7 datasets found.", $text);
    }
}