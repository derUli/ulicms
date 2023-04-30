<?php

use App\Helpers\TestHelper;

class SystemRequirementsTest extends \PHPUnit\Framework\TestCase {
    public function testMySQLVersion(): void {
        $this->assertTrue(
            \App\Utils\VersionComparison::compare($this->getMySQLVersion(), '5.5.3', '>=')
        );
    }

    public function testRootDirWritable() {
        $this->assertDirectoryIsWritable(ULICMS_ROOT);
    }

    public function testConnectToUliCMSServices(): void {
        $this->assertNotNull(file_get_contents_wrapper('https://www.ulicms.de/', true));
    }

    public function testIsRunningPHPUnit(): void {
        $this->assertTrue(TestHelper::isRunningPHPUnit());
    }

    private function getMySQLVersion() {
        $version = Database::getServerVersion();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        return $version;
    }
}
