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

        $actual = TestHelper::getOutput(static function() {
        $controller = new OneClickUpgradeController();
        $actual = $controller->accordionLayout();
        });

        $this->assertEmpty($actual);
    }

    public function testSettings(): void {
        $actual = TestHelper::getOutput(static function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            Settings::set('oneclick_upgrade_channel', 'slow');

            $controller = new OneClickUpgradeController();
            $controller->settings();
        });

        $this->assertStringNotContainsString('Changes were saved.', $actual);
        $this->assertStringContainsString('<option value="fast">', $actual);
        $this->assertStringContainsString('<option value="slow" selected>', $actual);
    }

    public function testSettingsSave(): void {
        $actual = TestHelper::getOutput(static function() {
            $_POST['oneclick_upgrade_channel'] = 'fast';
            $_SERVER['REQUEST_METHOD'] = 'POST';

            $controller = new OneClickUpgradeController();
            $controller->settings();
        });

        $this->assertStringContainsString('Changes were saved.', $actual);
        $this->assertStringContainsString('<option value="fast" selected>', $actual);
        $this->assertStringContainsString('<option value="slow">', $actual);
    }

    public function testGetSettingsHeadline(): void {
        $controller = new OneClickUpgradeController();
        $this->assertEquals('1Click Upgrade Settings', $controller->getSettingsHeadline());
    }
}
