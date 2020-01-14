<?php

class BackendHelperTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        require_once getLanguageFilePath("en");
    }

    public function testFormatDatasetCount0() {
        ob_start();
        BackendHelper::formatDatasetCount(0);
        $text = ob_get_clean();
        $this->assertEquals("0 datasets found.", $text);
    }

    public function testFormatDatasetCount1() {
        ob_start();
        BackendHelper::formatDatasetCount(1);
        $text = ob_get_clean();
        $this->assertEquals("One dataset found.", $text);
    }

    public function testFormatDatasetCount7() {
        ob_start();
        BackendHelper::formatDatasetCount(7);
        $text = ob_get_clean();
        $this->assertEquals("7 datasets found.", $text);
    }

    public function testSetAndGetActionIsSetGet() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        BackendHelper::setAction("pages");
        $this->assertEquals("pages", BackendHelper::getAction());
        unset($_REQUEST["action"]);
    }

    public function testSetAndGetActionIsSetPost() {
        $_SERVER["REQUEST_METHOD"] = "POST";
        BackendHelper::setAction("home");
        $this->assertEquals("home", BackendHelper::getAction());
        unset($_REQUEST["action"]);
    }

    public function testGetActionIsNotSet() {
        unset($_REQUEST["action"]);
        $this->assertEquals("home", BackendHelper::getAction());
    }

    public function testEnqueueEditorScripts() {
        BackendHelper::enqueueEditorScripts();
        $this->assertStringContainsString('<script src="content/cache/scripts/', $output = getCombinedScriptHtml());
    }
    
    public function testGetCKEditorSkins(){
        $skins = BackendHelper::getCKEditorSkins();
        
        $this->assertGreaterThanOrEqual(1, count($skins));
        $this->assertContains("moono", $skins);
    }

}
