<?php

use UliCMS\Utils\File;
use Spatie\Snapshots\MatchesSnapshots;

class FilesTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;
    
    public function testFileExtension() {
        $this->assertEquals("pdf", file_extension("myfile.pdf"));
        $this->assertEquals("pdf", file_extension("myfile.PDF"));
        $this->assertEquals("txt", file_extension("foo.txt"));
        $this->assertEquals("myfile", file_extension("myfile"));
    }

    public function testGetMime() {
        $this->assertEquals("text/plain", File::getMime(Path::resolve("ULICMS_ROOT/.htaccess")));
        $this->assertEquals("image/png", File::getMime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.png")));

        $this->assertNull(File::getMime('gibts nicht'));
    }

    public function testGetExtension() {
        $this->assertEquals("pdf", File::getExtension("myfile.pdf"));
        $this->assertEquals("pdf", File::getExtension("myfile.PDF"));
        $this->assertEquals("txt", File::getExtension("foo.txt"));
        $this->assertEquals("myfile", File::getExtension("myfile"));
    }

    public function testFindAllFolders() {
        $allFolders = find_all_folders("admin");
        $this->assertContains("admin/inc", $allFolders);
        $this->assertContains("admin/fm", $allFolders);
        $this->assertNotContains("admin/", $allFolders);
        $this->assertNotContains("vendor", $allFolders);
    }

    public function testFindAllFiles() {
        $allFiles = find_all_files("admin");
        $this->assertContains("admin/css/modern.scss", $allFiles);
        $this->assertContains("admin/gfx/logo.png", $allFiles);
        $this->assertNotContains("admin/.git", $allFiles);
        $this->assertNotContains("init.php", $allFiles);
    }

    public function testExistsLocallyExpectTrue() {
        $this->assertTrue(File::existsLocally(Path::resolve("ULICMS_ROOT/init.php")));
        $this->assertTrue(File::existsLocally(__FILE__));
    }

    public function testExistsLocallyExpectFalse() {
        $this->assertFalse(File::existsLocally("https://www.example.org"));
        $this->assertFalse(File::existsLocally("ftp://ftp.example.org"));
    }

    public function testToDataUri() {
        $this->assertNull(File::toDataUri("gibtsnicht.txt"));

        $this->assertMatchesTextSnapshot(File::toDataUri(Path::resolve("ULICMS_ROOT/admin/gfx/logo.png")));

        $this->assertMatchesTextSnapshot(
                File::toDataUri(Path::resolve("ULICMS_ROOT/tests/fixtures/hello-original.txt"), "application/inf")
        );
    }

    public function testDeleteIfExistsWithNonExistingFile() {
        $this->assertFalse(File::deleteIfExists("diese-datei-existiert-nicht"));
    }

    public function testDeleteIfExistsWithDirectory() {
        $dirs = Path::resolve("ULICMS_TMP/foo/bar");

        $baseDir = Path::resolve("ULICMS_TMP/foo");

        mkdir($dirs, 0777, true);

        $this->assertTrue(is_dir($dirs));

        $testFiles = array(
            "$baseDir/1",
            "$baseDir/2",
            "$baseDir/bar/1");

        foreach ($testFiles as $file) {
            $this->assertFalse(file_exists($file));
            file_put_contents($file, "hallo");
            $this->assertTrue(file_exists($file));
        }

        $this->assertTrue(File::deleteIfExists($baseDir));
        $this->assertFalse(File::deleteIfExists($baseDir));
    }

    public function testDeleteIfExistsWithFile() {
        $file = Path::Resolve("ULICMS_TMP/hello");
        file_put_contents($file, "world");

        $this->assertTrue(File::deleteIfExists($file));
        $this->assertFalse(File::deleteIfExists($file));
    }

    public function testSureRemoveDirIncludingItself() {
        $dirs = Path::resolve("ULICMS_TMP/foo/bar");

        $baseDir = Path::resolve("ULICMS_TMP/foo");

        mkdir($dirs, 0777, true);

        $this->assertTrue(is_dir($dirs));

        $testFiles = array(
            "$baseDir/1",
            "$baseDir/2",
            "$baseDir/bar/1");

        foreach ($testFiles as $file) {
            $this->assertFalse(file_exists($file));
            file_put_contents($file, "hallo");
            $this->assertTrue(file_exists($file));
        }

        sureRemoveDir($baseDir, true);

        sureRemoveDir("gibts_nicht", true);

        $this->assertFalse(is_dir($baseDir));
    }

    public function testSureRemoveDirWithoutItself() {
        $dirs = Path::resolve("ULICMS_TMP/foo/bar");

        $baseDir = Path::resolve("ULICMS_TMP/foo");

        mkdir($dirs, 0777, true);

        $this->assertTrue(is_dir($dirs));

        $testFiles = array(
            "$baseDir/1",
            "$baseDir/2",
            "$baseDir/bar/1");

        foreach ($testFiles as $file) {
            $this->assertFalse(file_exists($file));
            file_put_contents($file, "hallo");
            $this->assertTrue(file_exists($file));
        }

        sureRemoveDir($baseDir, false);

        $this->assertTrue(is_dir($baseDir));

        $this->assertCount(0, glob("$baseDir/*"));

        sureRemoveDir($baseDir, true);
    }

    public function testGetNewestMtimeNoFiles() {
        $this->assertEquals(0, File::getNewestMtime([]));
    }

    public function testGetNewestMtimeWithFiles() {
        $files = array(
            Path::resolve("ULICMS_ROOT/init.php"),
            Path::resolve("ULICMS_ROOT/composer.json"),
            Path::resolve("ULICMS_ROOT/lib/css/core.scss")
        );

        $minimumResult = mktime(0, 0, 0, 1, 1, 2019);

        $this->assertGreaterThanOrEqual($minimumResult, File::getNewestMtime($files));
    }

    public function testRecurseCopy() {
        $source = Path::resolve("ULICMS_ROOT/tests/fixtures");

        $destination = Path::resolve("ULICMS_TMP/copy-target");

        recurse_copy($source, $destination);

        $sourceFiles = find_all_files($source);
        $targetFiles = find_all_files($destination);

        $this->assertCount(count($sourceFiles), $targetFiles);
        sureRemoveDir($destination, true);
    }

    public function testLastChanged() {
        $file = Path::resolve("ULICMS_ROOT/package.json");

        ob_start();

        File::lastChanged($file);

        $timestamp = ob_get_clean();

        $this->assertIsNumeric($timestamp);
        $this->assertGreaterThanOrEqual(1, $timestamp);
    }

    public function testWriteAppendAndDeleteFile() {
        $path = Path::resolve(
                        "ULICMS_TMP/" . uniqid()
        );

        File::write($path, "foo");
        File::append($path, "bar");

        $fileContent = File::read($path);
        $this->assertEquals("foobar", $fileContent);
    }

    public function testWriteRenameAndDelete() {
        $path1 = Path::resolve(
                        "ULICMS_TMP/" . uniqid()
        );
        $path2 = Path::resolve(
                        "ULICMS_TMP/" . uniqid()
        );

        File::write($path1, "My File");

        $this->assertFileExists($path1);
        $this->assertFileDoesNotExist($path2);

        File::rename($path1, $path2);

        $this->assertFileDoesNotExist($path1);
        $this->assertFileExists($path2);

        File::delete($path2);

        $this->assertFileDoesNotExist($path1);
        $this->assertFileDoesNotExist($path2);
    }

    public function testLoadLinesAndTrim() {
        $inputFile = Path::resolve("ULICMS_ROOT/tests/fixtures/trimLines.input.txt");

        $output = File::loadLinesAndTrim($inputFile);
        $this->assertMatchesJsonSnapshot(json_encode($output));
    }

    public function testLoadLines() {
        $this->assertNull(
                File::loadLines("gibts_nicht")
        );
    }

}
