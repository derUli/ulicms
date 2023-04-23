<?php

use App\Backend\BackendPageRenderer;

use App\Helpers\TestHelper;

class BackendPageRendererTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        require_once getLanguageFilePath('en');
    }

    public function testBackendPageConstructorWithAction(): void {
        $renderer = new BackendPageRenderer('foo');
        $this->assertEquals('foo', $renderer->getAction());
    }

    public function testBackendPageConstructorWithActionAndModel(): void {
        $renderer = new BackendPageRenderer('foo', new Page());
        $this->assertEquals('foo', $renderer->getAction());
        $this->assertInstanceOf(Page::class, BackendPageRenderer::getModel());
    }

    public function testBackendPageSetAction(): void {
        $renderer = new BackendPageRenderer('foo');
        $renderer->setAction('bar');
        $this->assertEquals('bar', $renderer->getAction());
    }

    public function testBackendPageSetModel(): void {
        $renderer = new BackendPageRenderer('foo');
        BackendPageRenderer::setModel(new User());
        $this->assertInstanceOf(User::class, BackendPageRenderer::getModel());
    }

    public function testOutputMininified(): void {
        $output = TestHelper::getOutput(static function(): void {
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
