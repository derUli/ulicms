<?php

use App\Storages\ViewBag;

class ViewBagTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
       ViewBag::set('foo', 'bar');
       ViewBag::set('john', 'doe');
       ViewBag::set('hello', 'world');
    }

    protected function tearDown(): void
    {
       ViewBag::clear();
    }

    public function testSetAndGet()
    {
        $this->assertEquals('bar',ViewBag::get('foo'));
        $this->assertEquals('world',ViewBag::get('hello'));

       ViewBag::set('hello', 'you');
        $this->assertEquals('you',ViewBag::get('hello'));
    }

    public function testDelete()
    {
        $this->assertEquals('bar',ViewBag::get('foo'));

       ViewBag::delete('foo');
        $this->assertNull(\App\Storages\ViewBag::get('foo'));
    }

    public function testClear()
    {
        for($i = 0; $i < 100; $i++){
            ViewBag::set("foo_{$i}", $i);
        }

        for($i = 0; $i < 100; $i++){
            $this->assertEquals($i, ViewBag::get("foo_{$i}", $i));
        }

        ViewBag::clear();

        for($i = 0; $i < 100; $i++){
            $this->assertNull(ViewBag::get("foo_{$i}", $i));
        }
    }
}
