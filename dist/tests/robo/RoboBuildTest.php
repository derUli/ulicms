<?php

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

class RoboBuildTest extends RoboTestBase
{
    protected function setUp(): void {
        parent::setUp();

        FileSystem::write('.DS_STORE', 'foo');
    }

    protected function tearDown(): void {
        FileSystem::delete('.DS_STORE');
        FileSystem::createDir(ULICMS_TMP);

        parent::tearDown();
    }

    public function testBuildCopyChangelog(): void
    {
        $source = ULICMS_ROOT . '/../doc/changelog.txt';
        $target = ULICMS_CONTENT . '/modules/core_info/changelog.txt';

        FileSystem::delete($target);
        $this->assertFileDoesNotExist($target);

        $this->runRoboCommand(['build:copy-changelog']);
        $this->assertFileEquals($target, $source);
    }

    public function testBuildLicenses(): void
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

    public function testBuildDeleteBullshit(): void {
        $collectedBefore = Finder::find(
            [
                '.DS_STORE',
                'thumbs.db',
                '.thumbs',
                'tmp',
                '*.pyc'
            ])->collect();

        $this->assertNotCount(0, $collectedBefore);

        $this->runRoboCommand(['build:delete-bullshit']);

        $collectedAfter = Finder::find(
            [
                '.DS_STORE',
                'thumbs.db',
                '.thumbs',
                'tmp',
                '*.pyc'
            ])->collect();
            $this->assertCount(0, $collectedAfter);
    }
}
