<?php

declare(strict_types=1);

use App\Helpers\TestHelper;
use App\Translations\Translation;

class OneClickUpgradeControllerTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        require_once getLanguageFilePath('en');
        Translation::loadAllModuleLanguageFiles('en');
    }

    protected function tearDown(): void {
        unset($_SERVER['REQUEST_METHOD']);
        Settings::delete('oneclick_upgrade_channel');
    }

    public function testAccordionLayout(): void {

        $actual = TestHelper::getOutput(static function(): void {
            $controller = new OneClickUpgradeController();
            $actual = $controller->accordionLayout();
        });

        $this->assertEmpty($actual);
    }

    public function testSettings(): void {
        $actual = TestHelper::getOutput(static function(): void {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            Settings::set('oneclick_upgrade_channel', 'slow');

            $controller = new OneClickUpgradeController();
            $controller->settings();
        });

        $this->assertStringContainsString('<option value="fast">', $actual);
        $this->assertStringContainsString('<option value="slow" selected>', $actual);
    }

    public function testGetSettingsHeadline(): void {
        $controller = new OneClickUpgradeController();
        $this->assertEquals('1Click Upgrade Settings', $controller->getSettingsHeadline());
    }
}
