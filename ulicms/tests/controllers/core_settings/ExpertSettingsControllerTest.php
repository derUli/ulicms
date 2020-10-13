<?php

class ExpertSettingsControllerTest extends \PHPUnit\Framework\TestCase {

    protected function tearDown(): void {
        Settings::delete("foo");
    }

    public function testDeletePost() {
        Settings::set("foo", "bar");
        $this->assertEquals("bar", Settings::get("foo"));

        $controller = new ExpertSettingsController();
        $controller->_delete("foo");

        $this->assertNull(Settings::get("foo"));
    }

}
