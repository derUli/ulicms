<?php

declare(strict_types=1);

use App\Storages\Settings\MaintenanceMode;
use PHPUnit\Framework\TestCase;

class MaintenanceModeTest extends TestCase {
    protected function tearDown(): void {
        Settings::set('maintenance_mode', '0');
    }

    public function testEnable(): void {
        $maintenanceMode = MaintenanceMode::getInstance();
        $maintenanceMode->enable();
        $this->assertTrue($maintenanceMode->isEnabled());
    }

    public function testDisable(): void {
        $maintenanceMode = MaintenanceMode::getInstance();
        $maintenanceMode->disable();
        $this->assertFalse($maintenanceMode->isEnabled());
    }

    public function testToggle(): void {
        $maintenanceMode = MaintenanceMode::getInstance();
        $this->assertTrue($maintenanceMode->toggle());
        $this->assertFalse($maintenanceMode->toggle());
        $this->assertTrue($maintenanceMode->toggle());
    }
}
