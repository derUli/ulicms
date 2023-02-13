<?php

class ViewBagTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        ViewBag::set("foo", "bar");
        ViewBag::set("john", "doe");
        ViewBag::set("hello", "world");
    }

    protected function tearDown(): void
    {
        ViewBag::clear();
    }

    public function testSetAndGet()
    {
        $this->assertEquals("bar", ViewBag::get("foo"));
        $this->assertEquals("world", ViewBag::get("hello"));

        ViewBag::set("hello", "you");
        $this->assertEquals("you", ViewBag::get("hello"));
    }

    public function testGetAllVars()
    {
        $vars = ViewBag::getAllVars();
        $this->assertEquals("doe", $vars["john"]);
        $this->assertGreaterThanOrEqual(2, count($vars));
    }

    public function testDelete()
    {
        $this->assertEquals("bar", ViewBag::get("foo"));

        ViewBag::delete("foo");
        $this->assertNull(ViewBag::get("foo"));
    }

    public function testClear()
    {
        $this->assertGreaterThanOrEqual(1, count(ViewBag::getAllVars()));
        ViewBag::clear();
        $this->assertCount(0, ViewBag::getAllVars());
    }
}
