<?php

declare(strict_types=1);

class CoreUpgradeControllerTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        unset($_SERVER['REQUEST_METHOD']);

        $testFile = Path::resolve('ULICMS_ROOT/upgrade-check.txt');

        if(is_file($testFile)) {
            unlink($testFile);
        }
    }

    public function testGenerateCheckUrl(): void {
        $controller = new CoreUpgradeController();

        $this->assertEquals('https://channels.ulicms.de/2023.4/slow.json', $controller->getCheckUrl());
    }

    public function testSetCheckUrl(): void {
        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://example.org');

        $this->assertEquals('https://example.org', $controller->getCheckUrl());
    }

    public function testGetJson(): void {
        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://channels.ulicms.de/phpunit.json');
        $json = $controller->getJson();

        $this->assertEquals('2037.1', $json->version);
        $this->assertEquals('https://channels.ulicms.de/upgrade-test-dist.zip', $json->file);
        $this->assertEquals('161de9761c23d13b05d5c07d93f25e91', $json->hashsum);
    }

    public function testGetJsonFails(): void {
        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://this-url-does-not-exist');

        $this->assertNull($controller->getJson());
    }

    public function testCheckForUpgrades(): void {
        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://channels.ulicms.de/phpunit.json');

        $this->assertEquals('2037.1', $controller->checkForUpgrades());
    }

    public function testCheckForUpgradesFails(): void {
        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://this-url-does-not-exist');
        $this->assertNull($controller->checkForUpgrades());
    }

    public function testRunUpgradeFailsNoPermission(): void {
        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://channels.ulicms.de/phpunit.json');
        $this->assertFalse($controller->runUpgrade());
    }

    public function testRunUpgradeFailsFileIsNotJson(): void {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://channels.ulicms.de/phpunit2.json');
        $this->assertFalse($controller->runUpgrade(true));
    }

    public function testRunUpgradeSuccess(): void {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $controller = new CoreUpgradeController();
        $controller->setCheckUrl('https://channels.ulicms.de/phpunit.json');
        $this->assertTrue($controller->runUpgrade(true));
        $this->assertFileExists(Path::resolve('ULICMS_ROOT/upgrade-check.txt'));
    }
}
