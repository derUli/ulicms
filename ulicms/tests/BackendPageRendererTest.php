<?php
use UliCMS\Backend\BackendPageRenderer;

class BackendPageRendererTest extends \PHPUnit\Framework\TestCase
{

    public function testBackendPageConstructorWithAction()
    {
        $renderer = new BackendPageRenderer("foo");
        $this->assertEquals("foo", $renderer->getAction());
    }

    public function testBackendPageConstructorWithActionAndModel()
    {
        $renderer = new BackendPageRenderer("foo", new Page());
        $this->assertEquals("foo", $renderer->getAction());
        $this->assertInstanceOf(Page::class, BackendPageRenderer::getModel());
    }

    public function testBackendPageSetAction()
    {
        $renderer = new BackendPageRenderer("foo");
        $renderer->setAction("bar");
        $this->assertEquals("bar", $renderer->getAction());
    }

    public function testBackendPageSetModel()
    {
        $renderer = new BackendPageRenderer("foo");
        BackendPageRenderer::setModel(new User());
        $this->assertInstanceOf(User::class, BackendPageRenderer::getModel());
    }
}