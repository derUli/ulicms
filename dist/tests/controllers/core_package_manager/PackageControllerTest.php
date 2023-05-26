<?php

use App\Packages\PackageManager;
use App\Packages\SinPackageInstaller;

class PackageControllerTest extends \PHPUnit\Framework\TestCase {
    private $testUser = null;

    protected function setUp(): void {
        $user = new User();
        $user->setUsername('test-admin');
        $user->setLastname('Admin');
        $user->setFirstname('Der');
        $user->setPassword(uniqid());
        $user->setAdmin(true);
        $user->save();

        $this->testUser = $user;
        $_SESSION = [
            'login_id' => $user->getId()
        ];
    }

    protected function tearDown(): void {
        \App\Storages\Vars::delete('allModules');
        $module = new Module('fortune2');
        $module->enable();

        \App\Storages\ViewBag::delete('model');
        $this->testUser->delete();
        $_SESSION = [];
    }

    public function testGetPackageDownloadUrlReturnsUrl(): void {
        $controller = new PackageController();

        $this->assertEquals(
            'https://extend.ulicms.de/fortune2.html',
            $controller->_getPackageDownloadUrl('fortune2')
        );

        $this->assertEquals(
            'https://extend.ulicms.de/mail_queue.html',
            $controller->_getPackageDownloadUrl('mail_queue')
        );
    }

    public function testGetPackageDownloadUrlReturnsNull(): void {
        $controller = new PackageController();

        $this->assertNull($controller->_getPackageDownloadUrl('gibts_nicht'));
        $this->assertNull($controller->_getPackageDownloadUrl(''));
    }

    public function testAvailablePackages(): void {
        $controller = new PackageController();
        $output = $controller->_availablePackages();

        $this->assertGreaterThanOrEqual(3, substr_count($output, '<tr>'));

        $this->assertStringContainsString('bootstrap-3.3.7', $output);
        $this->assertStringContainsString('slicknav-1.0.10', $output);
    }

    public function testGetModuleInfo(): void {
        $controller = new PackageController();
        $output = $controller->_getModuleInfo('fortune2');

        $this->assertStringContainsString(
            '<h3>fortune2</h3>',
            $output
        );
        $this->assertStringContainsString(
            '<li>fortune2_post',
            $output
        );
        $this->assertStringContainsString(
            '<a href="https://extend.ulicms.de/fortune2.html" '
            . 'target="_blank">extend</a>',
            $output
        );
    }

    public function testGetThemeInfo(): void {
        $controller = new PackageController();
        $output = $controller->_getThemeInfo('impro17');

        $this->assertStringContainsString(
            '<h3>impro17</h3>',
            $output
        );
        $this->assertStringContainsString(
            '<a href="https://extend.ulicms.de/impro17.html" '
            . 'target="_blank">extend</a>',
            $output
        );
    }

    public function testGetLicenseReturnsString(): void {
        $controller = new PackageController();
        $output = $controller->_getPackageLicense('bootstrap');
        $this->assertStringContainsString('The MIT License (MIT)', $output);
    }

    public function testGetLicenseReturnsNull(): void {
        $controller = new PackageController();
        $output = $controller->_getPackageLicense('magic_package');
        $this->assertNull($output);
    }

    public function testToggleModule(): void {
        $module = new Module('fortune2');
        $module->disable();

        $controller = new PackageController();
        $this->assertEquals(
            [
                'name' => 'fortune2',
                'enabled' => true
            ],
            $controller->_toggleModule('fortune2')
        );
        $this->assertEquals(
            [
                'name' => 'fortune2',
                'enabled' => false
            ],
            $controller->_toggleModule('fortune2')
        );
    }

    public function testUninstallThemeReturnsTrue(): void {
        $this->installTheme2017();

        $controller = new PackageController();
        $success = $controller->_uninstallTheme('2017');

        $this->assertTrue($success);
        $this->assertNotContains('2017', getAllThemes());
    }

    public function testUninstallThemeReturnsFalse(): void {
        $controller = new PackageController();
        $success = $controller->_uninstallTheme('augenkrebs');
        $this->assertFalse($success);
    }

    public function testUninstallModuleReturnsTrue(): void {
        $this->installHelloWorld();

        $controller = new PackageController();
        $success = $controller->_uninstallModule('hello_world');

        \App\Storages\Vars::delete('allModules');
        $this->assertTrue($success);
        $this->assertNotContains('hello_world', getAllModules());
    }

    public function testUninstallModuleReturnsFalse(): void {
        $controller = new PackageController();
        $success = $controller->_uninstallModule('augenkrebs');
        $this->assertFalse($success);
    }

    protected function installTheme2017(): void {
        $packageFile = Path::resolve(
            'ULICMS_ROOT/tests/fixtures/packages/theme-2017-1.1.1.tar.gz'
        );

        $installer = new PackageManager();
        $installer->installPackage($packageFile);

        $this->assertContains('2017', getAllThemes());
    }

    protected function installHelloWorld(): void {
        $packageFile = Path::resolve(
            'ULICMS_ROOT/tests/fixtures/packages/hello_world-1.0.sin'
        );

        $installer = new SinPackageInstaller($packageFile);
        $installer->installPackage();

        \App\Storages\Vars::delete('allModules');

        $this->assertContains('hello_world', getAllModules());
    }
}
