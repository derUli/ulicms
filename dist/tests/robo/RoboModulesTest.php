<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboModulesTest extends RoboTestBase {
    protected function setUp(): void {
        $this->runRoboCommand(['modules:sync']);
    }

    public function testModulesList(): void {
        $output = $this->runRoboCommand(['modules:list']);

        $this->assertEquals(12, substr_count($output, 'core_'));
        foreach (getAllModules() as $module) {
            $this->assertStringContainsString($module, $output);
        }
    }

    public function testModulesListAll(): void {
        $output = $this->runRoboCommand(['modules:list', '[all]']);

        $this->assertEquals(12, substr_count($output, 'core_'));
        foreach (getAllModules() as $module) {
            $this->assertStringContainsString($module, $output);
        }
    }

    public function testModulesListCore(): void {
        $output = $this->runRoboCommand(['modules:list', '[core]']);
        $this->assertStringContainsString('core_', $output);

        $this->assertStringNotContainsString('bootstrap', $output);
        $this->assertStringNotContainsString('fortune2', $output);
        $this->assertStringNotContainsString('oneclick_upgrade', $output);
        $this->assertStringNotContainsString('slicknav', $output);
    }

    public function testModulesListExtend(): void {
        $output = $this->runRoboCommand(['modules:list', '[extend]']);
        $this->assertStringNotContainsString('update_manager_dashboard', $output);
        $this->assertStringNotContainsString('core_', $output);

        $this->assertStringContainsString('extend_upgrade_helper', $output);
        $this->assertStringContainsString('fortune2', $output);
        $this->assertStringContainsString('oneclick_upgrade', $output);
    }

    public function testModulesListPkgSrc(): void {
        $output = $this->runRoboCommand(['modules:list', '[pkgsrc]']);
        $this->assertStringContainsString('update_manager_dashboard', $output);

        $this->assertStringNotContainsString('core_', $output);
        $this->assertStringNotContainsString('extend_upgrade_helper', $output);
        $this->assertStringNotContainsString('fortune2', $output);
        $this->assertStringNotContainsString('oneclick_upgrade', $output);
    }

    public function testModulesGetPackageVersions(): void {
        $expected = file_get_contents(
            Path::resolve(
                'ULICMS_ROOT/tests/fixtures/robo/modulesGetPackageVersions.expected.txt'
            )
        );

        $actual = $this->runRoboCommand(
            [
                'modules:get-package-versions',
                'ldap_login'
            ]
        );

        $this->assertStringContainsString(
            '/content/files/packages/ldap_login/ldap_login-2.1.sin',
            $actual
        );
    }

    public function testModulesEnableAndDisable(): void {
        $actual = $this->runRoboCommand(
            [
                'modules:enable',
                'fortune2'
            ]
        );
        $this->assertEquals('fortune2 0.2.4 (enabled)', $actual);

        $actual = $this->runRoboCommand(
            [
                'modules:disable',
                'fortune2'
            ]
        );
        $this->assertEquals('fortune2 0.2.4 (disabled)', $actual);
    }

    public function testModulesToggle(): void {
        $this->runRoboCommand(
            [
                'modules:enable',
                'fortune2'
            ]
        );

        $actual = $this->runRoboCommand(
            [
                'modules:toggle',
                'fortune2'
            ]
        );
        $this->assertEquals('fortune2 0.2.4 (disabled)', $actual);

        $actual = $this->runRoboCommand(
            [
                'modules:toggle',
                'fortune2'
            ]
        );
        $this->assertEquals('fortune2 0.2.4 (enabled)', $actual);
    }

    public function testModulesRemoveReturnsError(): void {
        $actual = $this->runRoboCommand(
            [
                'modules:remove',
                'foobar1',
                'foobar2'
            ]
        );
        $this->assertStringContainsString('Removing foobar1 failed.', $actual);
        $this->assertStringContainsString('Removing foobar2 failed.', $actual);
    }
}
