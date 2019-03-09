<?php

class SimpleSettingsControllerTest extends \PHPUnit\Framework\TestCase {

    public function testGetTimezones() {
        $controller = ControllerRegistry::get("SimpleSettingsController");

        $timezones = $controller->getTimezones();
        $this->assertCount(425, $timezones);
        $this->assertContains("Europe/Berlin", $timezones);
        $this->assertContains("Asia/Tokyo", $timezones);
        $this->assertContains("Australia/Sydney", $timezones);
    }

}