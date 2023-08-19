<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboPackageTest extends RoboTestBase {
    protected function setUp(): void {
        $this->runRoboCommand(['modules:sync']);
    }

    protected function tearDown(): void {
        $moduleDir = \App\Utils\Path::resolve('ULICMS_ROOT/content/modules/hello_world');
        if (is_dir($moduleDir)) {
            sureRemoveDir($moduleDir);
        }
    }

    public function testPackagesList(): void {
        $output = $this->runRoboCommand(['packages:list']);

        $this->assertEquals(12, substr_count($output, 'core_'));

        $this->assertStringContainsString('2020 1.0.7', $output);
        $this->assertStringContainsString('impro17 2.1.8', $output);
    }

    public function testPackageExamineReturnsData(): void {
        $packageFile = \App\Utils\Path::resolve(
            'ULICMS_ROOT/tests/fixtures/packages/lock_inactive_users-1.0.1.sin'
        );

        $expected = file_get_contents(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/tests/fixtures/packages/packageExamine.expected.txt'
            )
        );

        $output = $this->runRoboCommand(['package:examine', $packageFile]);

        $this->assertEquals(
            trim(normalizeLN($expected)),
            trim(normalizeLN($output))
        );
    }

    public function testPackageExamineReturnsError(): void {
        $output = $this->runRoboCommand(
            ['package:examine', '../magic-1.0.sin']
        );
        $this->assertEquals('File magic-1.0.sin not found!', $output);
    }

    public function testPackagesInstallReturnsError(): void {
        $output = $this->runRoboCommand(
            ['package:install', '../magic-1.0.sin']
        );
        $this->assertEquals("Can't open ../magic-1.0.sin. File doesn't exists.", $output);
    }

    public function testPackageInstallWithSinFile(): void {
        $packageFile = \App\Utils\Path::resolve(
            'ULICMS_ROOT/tests/fixtures/packages/hello_world-1.0.sin'
        );
        $installOutput = $this->runRoboCommand(
            ['package:install', $packageFile]
        );
        $this->assertEquals(
            'Package hello_world-1.0.sin successfully installed',
            $installOutput
        );

        \App\Storages\Vars::delete('allModules');
        $this->assertContains('hello_world', getAllModules());

        $removeOutput = $this->runRoboCommand(
            ['modules:remove', 'hello_world']
        );

        \App\Storages\Vars::delete('allModules');
        $this->assertEquals('Package hello_world removed.', $removeOutput);
        $this->assertNotContains('hello_world', getAllModules());
    }

    public function testPackageInstallWithTarGzFile(): void {
        $packageFile = \App\Utils\Path::resolve(
            'ULICMS_ROOT/tests/fixtures/packages/hello_world-1.0.tar.gz'
        );
        $installOutput = $this->runRoboCommand(
            ['package:install', $packageFile]
        );
        $this->assertEquals(
            'Package hello_world-1.0.tar.gz successfully installed',
            $installOutput
        );

        \App\Storages\Vars::delete('allModules');
        $this->assertContains('hello_world', getAllModules());
    }

    public function testPackageInstallReturnsError(): void {
        $packageFile = \App\Utils\Path::resolve(
            'ULICMS_ROOT/tests/fixtures/packages/error-1.0.sin'
        );
        $output = $this->runRoboCommand(
            ['package:install', $packageFile]
        );

        $this->assertStringContainsString(
            'Installation of package error-1.0.sin failed.',
            $output
        );
        $this->assertStringContainsString(
            'Depedency foobar is not installed.',
            $output
        );
        $this->assertStringContainsString(
            'The package is not compatible with your UliCMS Version.',
            $output
        );
    }
}
