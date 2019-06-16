<?php

class VarsTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        Vars::set("foo", "bar");
        Vars::set("john", "doe");
        Vars::set("hello", "world");
    }

    public function tearDown() {
        Vars::clear();
    }

    public function testSetAndGet() {

        $this->assertEquals("bar", Vars::get("foo"));
        $this->assertEquals("world", Vars::get("hello"));

        Vars::set("hello", "you");
        $this->assertEquals("you", Vars::get("hello"));
    }

    public function testGetAllVars() {
        $vars = Vars::getAllVars();
        $this->assertEquals("doe", $vars["john"]);
        $this->assertGreaterThanOrEqual(2, count($vars));
    }

    public function testDelete() {
        $this->assertEquals("bar", Vars::get("foo"));

        Vars::delete("foo");
        $this->assertNull(Vars::get("foo"));
    }

    public function testClear() {
        $this->assertGreaterThanOrEqual(1, count(Vars::getAllVars()));
        Vars::clear();
        $this->assertCount(0, Vars::getAllVars());
    }

}
