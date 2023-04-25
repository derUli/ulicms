<?php

use App\Storages\Vars;
use App\UliCMS\CoreBootstrap;
use PHPUnit\Framework\TestCase;

class CoreBootstraptest extends TestCase {
    public function testCheckConfigExists(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertTrue($coreBootstrap->checkConfigExists());
    }

    public function testGetInstallerUrl(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertStringStartsWith('./installer', $coreBootstrap->getInstallerUrl());
    }

    public function testIsFreshDeploy(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertFalse($coreBootstrap->isFreshDeploy());
    }

    public function testPostDeployUpdate(): void {
        Settings::delete('initialized');
        $this->assertNull(Settings::get('initialized'));

        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->postDeployUpdate();

        $this->assertIsNumeric(Settings::get('initialized'));
    }

    public function testInitStorages(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->initStorages();

        $this->assertCount(0, Vars::get('http_headers'));
    }

    public function testLoadEnvFile(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->loadEnvFile();

        $this->assertIsInt(umask());
        $this->assertIsBool(is_debug_mode());
    }
}