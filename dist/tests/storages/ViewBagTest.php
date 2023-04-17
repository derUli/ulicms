<?php

class ViewBagTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \App\Storages\ViewBag::set('foo', 'bar');
        \App\Storages\ViewBag::set('john', 'doe');
        \App\Storages\ViewBag::set('hello', 'world');
    }

    protected function tearDown(): void
    {
        \App\Storages\ViewBag::clear();
    }

    public function testSetAndGet()
    {
        $this->assertEquals('bar', \App\Storages\ViewBag::get('foo'));
        $this->assertEquals('world', \App\Storages\ViewBag::get('hello'));

        \App\Storages\ViewBag::set('hello', 'you');
        $this->assertEquals('you', \App\Storages\ViewBag::get('hello'));
    }

    public function testgetAll()
    {
        $vars = \App\Storages\ViewBag::getAll();
        $this->assertEquals('doe', $vars['john']);
        $this->assertGreaterThanOrEqual(2, count($vars));
    }

    public function testDelete()
    {
        $this->assertEquals('bar', \App\Storages\ViewBag::get('foo'));

        \App\Storages\ViewBag::delete('foo');
        $this->assertNull(\App\Storages\ViewBag::get('foo'));
    }

    public function testClear()
    {
        $this->assertGreaterThanOrEqual(1, count(\App\Storages\ViewBag::getAll()));
        \App\Storages\ViewBag::clear();
        $this->assertCount(0, \App\Storages\ViewBag::getAll());
    }
}
