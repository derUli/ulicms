<?php

use App\Storages\Vars;

class VarsTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        Vars::set('foo', 'bar');
        Vars::set('john', 'doe');
        Vars::set('hello', 'world');
    }

    protected function tearDown(): void {
        Vars::clear();
    }

    public function testSetAndGet(): void {
        $this->assertEquals('bar', Vars::get('foo'));
        $this->assertEquals('world', Vars::get('hello'));

        Vars::set('hello', 'you');
        $this->assertEquals('you', Vars::get('hello'));
    }

    public function testDelete(): void {
        $this->assertEquals('bar', Vars::get('foo'));

        Vars::delete('foo');
        $this->assertNull(Vars::get('foo'));
    }

    public function testClear(): void {
        for($i = 0; $i < 100; $i++) {
            Vars::set("foo_{$i}", $i);
        }

        for($i = 0; $i < 100; $i++) {
            $this->assertEquals($i, Vars::get("foo_{$i}", $i));
        }

        Vars::clear();

        for($i = 0; $i < 100; $i++) {
            $this->assertNull(Vars::get("foo_{$i}", $i));
        }
    }
}
