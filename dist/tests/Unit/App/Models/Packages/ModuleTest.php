<?php

class ModuleTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $manager = new \App\Packages\ModuleManager();
        $manager->sync();
        $this->backupFortune2();

        $module = new \App\Models\Packages\Module('core_comments');
        $module->enable();
        Settings::delete('fortune2_uninstalled_at');
    }

    protected function tearDown(): void {
        $this->restoreFortune2();
        $manager = new \App\Packages\ModuleManager();
        $manager->sync();

        $module = new \App\Models\Packages\Module('core_comments');
        $module->enable();
    }

    public function testHasAdminPageReturnsTrue(): void {
        $module = new \App\Models\Packages\Module('fortune2');
        $this->assertTrue($module->hasAdminPage());
    }

    public function testHasAdminPageReturnsFalse(): void {
        $module = new \App\Models\Packages\Module('core_users');
        $this->assertFalse($module->hasAdminPage());
    }

    public function testisEmbedModuleReturnsTrue(): void {
        $module = new \App\Models\Packages\Module('fortune2');
        $this->assertTrue($module->isEmbedModule());
    }

    public function testisEmbedModuleReturnsFalse(): void {
        $module = new \App\Models\Packages\Module('core_users');
        $this->assertFalse($module->isEmbedModule());
    }

    public function testCreateUpdateAndDeleteModule(): void {
        $module = new \App\Models\Packages\Module();
        $module->setName('my_awesome_module');
        $module->setVersion('1.0');
        $module->save();

        $module = new \App\Models\Packages\Module('my_awesome_module');
        $this->assertEquals('my_awesome_module', $module->getName());
        $this->assertEquals('1.0', $module->getVersion());
        $this->assertFalse($module->isEnabled());

        $module->setVersion('1.1');
        $module->save();

        $module = new \App\Models\Packages\Module('my_awesome_module');

        $this->assertEquals('my_awesome_module', $module->getName());
        $this->assertEquals('1.1', $module->getVersion());
        $this->assertFalse($module->isEnabled());

        $module->enable();
        $this->assertTrue($module->isEnabled());

        $module = new \App\Models\Packages\Module('my_awesome_module');
        $this->assertTrue($module->isEnabled());
        $module->disable();

        $module = new \App\Models\Packages\Module('my_awesome_module');
        $this->assertFalse($module->isEnabled());

        $this->assertTrue($module->delete());

        // can't delete an already deleted dataset
        $this->assertNull($module->delete());

        $module = new \App\Models\Packages\Module('my_awesome_module');
        $this->assertNull($module->getName());
        $this->assertNull($module->getVersion());
    }

    public function testToggleEnabled(): void {
        $module = new \App\Models\Packages\Module('fortune2');
        $module->toggleEnabled();
        $this->assertFalse($module->isEnabled());

        $module = new \App\Models\Packages\Module('fortune2');
        $this->assertFalse($module->isEnabled());

        $module->toggleEnabled();

        $this->assertTrue($module->isEnabled());

        $module = new \App\Models\Packages\Module('fortune2');
        $this->assertTrue($module->isEnabled());
    }

    public function testIsInstalledReturnsTrue(): void {
        $module = new \App\Models\Packages\Module('core_content');
        $this->assertTrue($module->isInstalled());
    }

    public function testIsInstalledReturnsFalse(): void {
        $module = new \App\Models\Packages\Module('not_existing_module');
        $this->assertFalse($module->isInstalled());
    }

    public function testGetShortcodeReturnsShortcode(): void {
        $module = new \App\Models\Packages\Module();
        $module->setName('hello_world');
        $this->assertEquals('[module=hello_world]', $module->getShortCode());
    }

    public function testGetShortcodeReturnsNull(): void {
        $module = new \App\Models\Packages\Module();
        $this->assertNull(
            $module->getShortCode()
        );
    }

    public function testGetDependentModules(): void {
        $module = new \App\Models\Packages\Module('core_content');

        $this->assertContains(
            'core_comments',
            $module->getDependentModules()
        );
    }

    public function testhasUninstallEventReturnsTrue(): void {
        $module = new \App\Models\Packages\Module('fortune2');
        $this->assertTrue($module->hasUninstallEvent());
    }

    public function testhasUninstallEventReturnsFalse(): void {
        $module = new \App\Models\Packages\Module('core_content');
        $this->assertFalse($module->hasUninstallEvent());
    }

    public function testUninstallReturnsTrue(): void {
        $module = new \App\Models\Packages\Module('fortune2');
        $this->assertTrue($module->uninstall());

        $fortune2_uninstalled_at = Settings::get(
            'fortune2_uninstalled_at',
            'int'
        );

        $this->assertGreaterThanOrEqual(time() - 10, $fortune2_uninstalled_at);
    }

    protected function backupFortune2(): void {
        $src = getModulePath('fortune2', true);
        $dst = getModulePath('.fortune2.bak', true);
        if (is_dir($src) && ! is_dir($dst)) {
            recurse_copy($src, $dst);
        }
        Settings::delete('fortune2_uninstalled_at');
    }

    protected function restoreFortune2(): void {
        $src = getModulePath('.fortune2.bak', true);
        $dst = getModulePath('fortune2', true);

        if (is_dir($src) && ! is_dir($dst)) {
            recurse_copy($src, $dst);
            sureRemoveDir($src, true);
        }
    }
}
