<?php

class BackendHelperTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        require_once getLanguageFilePath('en');
    }

    public function testSetAndGetActionIsSetGet(): void {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        \App\Helpers\BackendHelper::setAction('pages');
        $this->assertEquals('pages', \App\Helpers\BackendHelper::getAction());
        unset($_REQUEST['action']);
    }

    public function testSetAndGetActionIsSetPost(): void {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        \App\Helpers\BackendHelper::setAction('home');
        $this->assertEquals('home', \App\Helpers\BackendHelper::getAction());
        unset($_REQUEST['action']);
    }

    public function testGetActionIsNotSet(): void {
        unset($_REQUEST['action']);
        $this->assertEquals('home', \App\Helpers\BackendHelper::getAction());
    }

    public function testEnqueueEditorScripts(): void {
        ob_start();
        \App\Helpers\BackendHelper::enqueueEditorScripts();
        ob_get_clean();
        $this->assertStringContainsString(
            '<script src="content/generated/public/scripts/',
            getCombinedScriptHtml()
        );
    }
}
