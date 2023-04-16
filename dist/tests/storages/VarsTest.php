<?php

class VarsTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \App\Storages\Vars::set('foo', 'bar');
        \App\Storages\Vars::set('john', 'doe');
        \App\Storages\Vars::set('hello', 'world');
    }

    protected function tearDown(): void
    {
        \App\Storages\Vars::clear();
    }

    public function testSetAndGet()
    {
        $this->assertEquals('bar', \App\Storages\Vars::get('foo'));
        $this->assertEquals('world', \App\Storages\Vars::get('hello'));

        \App\Storages\Vars::set('hello', 'you');
        $this->assertEquals('you', \App\Storages\Vars::get('hello'));
    }

    public function testGetAllVars()
    {
        $vars = \App\Storages\Vars::getAllVars();
        $this->assertEquals('doe', $vars['john']);
        $this->assertGreaterThanOrEqual(2, count($vars));
    }

    public function testDelete()
    {
        $this->assertEquals('bar', \App\Storages\Vars::get('foo'));

        \App\Storages\Vars::delete('foo');
        $this->assertNull(\App\Storages\Vars::get('foo'));
    }

    public function testClear()
    {
        $this->assertGreaterThanOrEqual(1, count(\App\Storages\Vars::getAllVars()));
        \App\Storages\Vars::clear();
        $this->assertCount(0, \App\Storages\Vars::getAllVars());
    }
}
