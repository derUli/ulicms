<?php

class BackendHelperTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        require_once getLanguageFilePath('en');
    }

    public function testSetAndGetActionIsSetGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        BackendHelper::setAction('pages');
        $this->assertEquals('pages', BackendHelper::getAction());
        unset($_REQUEST['action']);
    }

    public function testSetAndGetActionIsSetPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        BackendHelper::setAction('home');
        $this->assertEquals('home', BackendHelper::getAction());
        unset($_REQUEST['action']);
    }

    public function testGetActionIsNotSet()
    {
        unset($_REQUEST['action']);
        $this->assertEquals('home', BackendHelper::getAction());
    }

    public function testEnqueueEditorScripts()
    {
        ob_start();
        BackendHelper::enqueueEditorScripts();
        ob_get_clean();
        $this->assertStringContainsString(
            '<script src="content/generated/public/scripts/',
            getCombinedScriptHtml()
        );
    }

    public function testGetCKEditorSkins()
    {
        $skins = BackendHelper::getCKEditorSkins();

        $this->assertGreaterThanOrEqual(1, count($skins));
        $this->assertContains('moono-lisa', $skins);
    }
}
