<?php

use App\Utils\File;

use Spatie\Snapshots\MatchesSnapshots;

class FileTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    public function testGetMime(): void {
        $this->assertEquals('text/plain', File::getMime(\App\Utils\Path::resolve('ULICMS_ROOT/.htaccess')));
        $this->assertEquals('image/png', File::getMime(\App\Utils\Path::resolve('ULICMS_ROOT/admin/gfx/edit.png')));
    }

    public function testGetExtension(): void {
        $this->assertEquals('pdf', File::getExtension('myfile.pdf'));
        $this->assertEquals('pdf', File::getExtension('myfile.PDF'));
        $this->assertEquals('txt', File::getExtension('foo.txt'));
    }

    public function testFindAllDirs(): void {
        $allFolders = File::findAllDirs('admin');
        $this->assertContains('admin/inc', $allFolders);
        $this->assertContains('admin/fm', $allFolders);
        $this->assertNotContains('admin/', $allFolders);
        $this->assertNotContains('vendor', $allFolders);
    }

    public function testFindAllFiles(): void {
        $allFiles = File::findAllFiles('admin');
        $this->assertContains('admin/css/main.scss', $allFiles);
        $this->assertContains('admin/gfx/logo.png', $allFiles);
        $this->assertNotContains('admin/.git', $allFiles);
        $this->assertNotContains('init.php', $allFiles);
    }

    public function testToDataUri(): void {
        $this->assertNull(File::toDataUri('gibtsnicht.txt'));

        $this->assertMatchesTextSnapshot(
            File::toDataUri(\App\Utils\Path::resolve('ULICMS_ROOT/admin/gfx/logo.png'))
        );

        $this->assertMatchesTextSnapshot(
            File::toDataUri(\App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/hello-original.txt'), 'application/inf')
        );
    }

    public function testDeleteIfExistsWithNonExistingFile(): void {
        $this->assertFalse(File::deleteIfExists('diese-datei-existiert-nicht'));
    }

    public function testDeleteIfExistsWithDirectory(): void {
        $dirs = \App\Utils\Path::resolve('ULICMS_TMP/foo/bar');

        $baseDir = \App\Utils\Path::resolve('ULICMS_TMP/foo');

        mkdir($dirs, 0777, true);

        $this->assertTrue(is_dir($dirs));

        $testFiles = [
            "{$baseDir}/1",
            "{$baseDir}/2",
            "{$baseDir}/bar/1"];

        foreach ($testFiles as $file) {
            $this->assertFalse(is_file($file));
            file_put_contents($file, 'hallo');
            $this->assertTrue(is_file($file));
        }

        $this->assertTrue(File::deleteIfExists($baseDir));
        $this->assertFalse(File::deleteIfExists($baseDir));
    }

    public function testDeleteIfExistsWithFile(): void {
        $file = \App\Utils\Path::Resolve('ULICMS_TMP/hello');
        file_put_contents($file, 'world');

        $this->assertTrue(File::deleteIfExists($file));
        $this->assertFalse(File::deleteIfExists($file));
    }

    public function testSureRemoveDirIncludingItself(): void {
        $dirs = \App\Utils\Path::resolve('ULICMS_TMP/foo/bar');

        $baseDir = \App\Utils\Path::resolve('ULICMS_TMP/foo');

        mkdir($dirs, 0777, true);

        $this->assertTrue(is_dir($dirs));

        $testFiles = [
            "{$baseDir}/1",
            "{$baseDir}/2",
            "{$baseDir}/bar/1"];

        foreach ($testFiles as $file) {
            $this->assertFalse(is_file($file));
            file_put_contents($file, 'hallo');
            $this->assertTrue(is_file($file));
        }

        sureRemoveDir($baseDir, true);

        sureRemoveDir('gibts_nicht', true);

        $this->assertFalse(is_dir($baseDir));
    }

    public function testSureRemoveDirWithoutItself(): void {
        $dirs = \App\Utils\Path::resolve('ULICMS_TMP/foo/bar');

        $baseDir = \App\Utils\Path::resolve('ULICMS_TMP/foo');

        mkdir($dirs, 0777, true);

        $this->assertTrue(is_dir($dirs));

        $testFiles = [
            "{$baseDir}/1",
            "{$baseDir}/2",
            "{$baseDir}/bar/1"];

        foreach ($testFiles as $file) {
            $this->assertFalse(is_file($file));
            file_put_contents($file, 'hallo');
            $this->assertTrue(is_file($file));
        }

        sureRemoveDir($baseDir, false);

        $this->assertTrue(is_dir($baseDir));

        $this->assertCount(0, glob("{$baseDir}/*"));

        sureRemoveDir($baseDir, true);
    }

    public function testGetNewestMtimeNoFiles(): void {
        $this->assertEquals(0, File::getNewestMtime([]));
    }

    public function testGetNewestMtimeWithFiles(): void {
        $files = [
            \App\Utils\Path::resolve('ULICMS_ROOT/init.php'),
            \App\Utils\Path::resolve('ULICMS_ROOT/composer.json'),
            \App\Utils\Path::resolve('ULICMS_ROOT/lib/css/core.scss')
        ];

        $minimumResult = mktime(0, 0, 0, 1, 1, 2019);

        $this->assertGreaterThanOrEqual($minimumResult, File::getNewestMtime($files));
    }

    public function testRecurseCopy(): void {
        $source = \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures');

        $destination = \App\Utils\Path::resolve('ULICMS_TMP/copy-target');

        recurse_copy($source, $destination);

        $sourceFiles = File::findAllFiles($source);
        $targetFiles = File::findAllFiles($destination);

        $this->assertCount(count($sourceFiles), $targetFiles);
        sureRemoveDir($destination, true);
    }

    public function testGetLastChanged(): void {
        $source = \App\Utils\Path::resolve('ULICMS_ROOT/composer.json');
        $lastChanged = File::getLastChanged($source);
        $this->assertGreaterThanOrEqual(1680824251, $lastChanged);
    }
}
