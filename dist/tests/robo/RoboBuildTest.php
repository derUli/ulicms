<?php

use Nette\Utils\FileSystem;

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboBuildTest extends RoboTestBase
{
    public function testBuildCopyChangelog()
    {
        $source = ULICMS_ROOT . '/../doc/changelog.txt';
        $target = ULICMS_CONTENT . '/modules/core_info/changelog.txt';

        FileSystem::delete($target);
        $this->assertFileDoesNotExist($target);

        $this->runRoboCommand(['build:copy-changelog']);
        $this->assertFileEquals($target, $source);
    }

    public function testBuildLicenses()
    {
        $file1 = ULICMS_ROOT . '/licenses.md';
        $file2 = ULICMS_ROOT . '/licenses.json';

        FileSystem::delete($file1);
        FileSystem::delete($file2);

        $this->assertFileDoesNotExist($file1);
        $this->assertFileDoesNotExist($file2);

        $this->runRoboCommand(['build:licenses']);

        $this->assertFileExists($file1);
        $this->assertFileExists($file2);

        $this->assertIsArray(json_decode(file_get_contents($file2)), true);
    }
}
