<?php

use App\Backend\BackendPageRenderer;

use App\Helpers\TestHelper;

class BackendPageRendererTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        require_once getLanguageFilePath('en');
    }

    public function testBackendPageConstructorWithAction() {
        $renderer = new BackendPageRenderer('foo');
        $this->assertEquals('foo', $renderer->getAction());
    }

    public function testBackendPageConstructorWithActionAndModel() {
        $renderer = new BackendPageRenderer('foo', new Page());
        $this->assertEquals('foo', $renderer->getAction());
        $this->assertInstanceOf(Page::class, BackendPageRenderer::getModel());
    }

    public function testBackendPageSetAction() {
        $renderer = new BackendPageRenderer('foo');
        $renderer->setAction('bar');
        $this->assertEquals('bar', $renderer->getAction());
    }

    public function testBackendPageSetModel() {
        $renderer = new BackendPageRenderer('foo');
        BackendPageRenderer::setModel(new User());
        $this->assertInstanceOf(User::class, BackendPageRenderer::getModel());
    }

    public function testOutputMininified() {
        $output = TestHelper::getOutput(static function() {
            $renderer = new BackendPageRenderer('foo');
            ob_start();
            echo '<div     class="hello"> Hello    World</div>  ';
            $renderer->outputMinified();
        });

        $renderer = new BackendPageRenderer('foo');
        $renderer->doCronEvents();

        $this->assertEquals('<div class="hello">Hello World</div>', $output);
    }
}
