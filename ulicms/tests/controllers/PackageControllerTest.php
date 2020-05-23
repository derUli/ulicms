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

}
