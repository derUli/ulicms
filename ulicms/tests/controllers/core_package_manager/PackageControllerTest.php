<?php

class PackageControllerTest extends \PHPUnit\Framework\TestCase {

    private $testUser = null;

    public function setUp() {
        $user = new User();
        $user->setUsername("test-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Der");
        $user->setPassword(uniqid());
        $user->setAdmin(true);
        $user->save();

        $this->testUser = $user;
        $_SESSION = [
            "login_id" => $user->getId()
        ];
    }

    public function tearDown() {
        Vars::delete("allModules");
        $module = new Module("fortune2");
        $module->enable();

        ViewBag::delete("model");
        $this->testUser->delete();
        $_SESSION = [];
    }

    public function testGetPackageDownloadUrlReturnsUrl() {
        $controller = new PackageController();

        $this->assertEquals(
                "https://extend.ulicms.de/fortune2.html",
                $controller->_getPackageDownloadUrl("fortune2")
        );


        $this->assertEquals(
                "https://extend.ulicms.de/mail_queue.html",
                $controller->_getPackageDownloadUrl("mail_queue")
        );
    }

    public function testGetPackageDownloadUrlReturnsNull() {
        $controller = new PackageController();

        $this->assertNull($controller->_getPackageDownloadUrl("gibts_nicht"));
        $this->assertNull($controller->_getPackageDownloadUrl(""));
    }

    public function testTruncateInstalledPatches() {
        $controller = new PackageController();
        $controller->_truncateInstalledPatches();

        $datasets = Database::selectAll("installed_patches");
        $this->assertEquals(0, Database::getNumRows($datasets));
    }

    public function testAvailablePackages() {
        $controller = new PackageController();
        $output = $controller->_availablePackages();

        $this->assertGreaterThanOrEqual(100, substr_count($output, "<tr>"));
        $this->assertGreaterThanOrEqual(15, substr_count($output, "theme-"));


        $this->assertStringContainsString("android_toolbar_color-1.2", $output);
        $this->assertStringContainsString("bootstrap-3.3.7", $output);
    }

    public function testGetModuleInfo() {
        $controller = new PackageController();
        $output = $controller->_getModuleInfo("fortune2");

        $this->assertStringContainsString(
                "<h3>fortune2</h3>",
                $output
        );
        $this->assertStringContainsString(
                "<li>fortune2_post",
                $output
        );
        $this->assertStringContainsString(
                '<a href="https://extend.ulicms.de/fortune2.html" '
                . 'target="_blank">extend</a>',
                $output
        );
    }

    public function testGetThemeInfo() {
        $controller = new PackageController();
        $output = $controller->_getThemeInfo("impro17");

        $this->assertStringContainsString(
                "<h3>impro17</h3>",
                $output
        );
        $this->assertStringContainsString(
                "<li>output_design_settings_styles</li>",
                $output
        );
        $this->assertStringContainsString(
                '<a href="https://extend.ulicms.de/impro17.html" '
                . 'target="_blank">extend</a>',
                $output
        );
    }

    public function testGetLicenseReturnsString() {
        $controller = new PackageController();
        $output = $controller->_getPackageLicense("adminer");
        $this->assertStringContainsString("Apache License or GPL2", $output);
    }

    public function testGetLicenseReturnsNull() {
        $controller = new PackageController();
        $output = $controller->_getPackageLicense("magic_package");
        $this->assertNull($output);
    }

    public function testToggleModule() {
        $module = new Module("fortune2");
        $module->disable();

        $controller = new PackageController();
        $this->assertEquals(
                [
                    "name" => "fortune2",
                    "enabled" => true
                ],
                $controller->_toggleModule("fortune2")
        );
        $this->assertEquals(
                [
                    "name" => "fortune2",
                    "enabled" => false
                ],
                $controller->_toggleModule("fortune2")
        );
    }

    public function testUninstallThemeReturnsTrue() {
        $this->installTheme2017();

        $controller = new PackageController();
        $success = $controller->_uninstallTheme("2017");

        $this->assertTrue($success);
        $this->assertNotContains("2017", getAllThemes());
    }

    public function testUninstallThemeReturnsFalse() {
        $controller = new PackageController();
        $success = $controller->_uninstallTheme("augenkrebs");
        $this->assertFalse($success);
    }

    protected function installTheme2017() {
        $packageFile = Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/packages/theme-2017-1.1.1.tar.gz"
        );

        $installer = new PackageManager();
        $installer->installPackage($packageFile);

        $this->assertContains("2017", getAllThemes());
    }

    public function testUninstallModuleReturnsTrue() {
        $this->installHelloWorld();

        $controller = new PackageController();
        $success = $controller->_uninstallModule("hello_world");

        Vars::delete("allModules");
        $this->assertTrue($success);
        $this->assertNotContains("hello_world", getAllModules());
    }

    public function testUninstallModuleReturnsFalse() {
        $controller = new PackageController();
        $success = $controller->_uninstallModule("augenkrebs");
        $this->assertFalse($success);
    }

    protected function installHelloWorld() {
        $packageFile = Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/packages/hello_world-1.0.sin"
        );

        $installer = new SinPackageInstaller($packageFile);
        $installer->installPackage();

        Vars::delete("allModules");

        $this->assertContains("hello_world", getAllModules());
    }

}
