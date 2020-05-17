<?php

use UliCMS\Backend\BackendPageRenderer;
use UliCMS\Backend\Utils\BrowserCompatiblityChecker;
use UliCMS\Helpers\TestHelper;

class BackendPageRendererTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        require_once getLanguageFilePath("en");
    }

    public function testBackendPageConstructorWithAction() {
        $renderer = new BackendPageRenderer("foo");
        $this->assertEquals("foo", $renderer->getAction());
    }

    public function testBackendPageConstructorWithActionAndModel() {
        $renderer = new BackendPageRenderer("foo", new Page());
        $this->assertEquals("foo", $renderer->getAction());
        $this->assertInstanceOf(Page::class, BackendPageRenderer::getModel());
    }

    public function testBackendPageSetAction() {
        $renderer = new BackendPageRenderer("foo");
        $renderer->setAction("bar");
        $this->assertEquals("bar", $renderer->getAction());
    }

    public function testBackendPageSetModel() {
        $renderer = new BackendPageRenderer("foo");
        BackendPageRenderer::setModel(new User());
        $this->assertInstanceOf(User::class, BackendPageRenderer::getModel());
    }

    public function testShowUnsupportedBrowser() {
        $output = TestHelper::getOutput(function() {
                $checker = new BrowserCompatiblityChecker(
                            "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; "
                            . ".NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; "
                            . ".NET CLR 3.5.30729; Zoom 3.6.0; rv:11.0) like Gecko"
                    );
                    $checker->isCompatible();
                    $renderer = new BackendPageRenderer("foo");

                    $renderer->showUnsupportedBrowser($checker);
                });
        $this->assertStringContainsString(
                "Your are using the unsupported browser Microsoft Internet Explorer",
                $output
        );
    }

}
