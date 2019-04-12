<?php

class FilesTest extends \PHPUnit\Framework\TestCase {

    public function testFileExtension() {
        $this->assertEquals("pdf", file_extension("myfile.pdf"));
        $this->assertEquals("pdf", file_extension("myfile.PDF"));
        $this->assertEquals("txt", file_extension("foo.txt"));
        $this->assertEquals("myfile", file_extension("myfile"));
    }

    public function testGetMime() {
        $this->assertEquals("text/plain", File::getMime(Path::resolve("ULICMS_ROOT/.htaccess")));
        $this->assertEquals("image/gif", File::getMime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.gif")));
        $this->assertEquals("image/png", File::getMime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.png")));
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
        $this->assertContains("admin/kcfinder", $allFolders);
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
        $this->assertTrue(File::existsLocally("init.php"));
        $this->assertTrue(File::existsLocally(__FILE__));
    }

    public function testExistsLocallyExpectFalse() {
        $this->assertFalse(File::existsLocally("https://www.example.org"));
        $this->assertFalse(File::existsLocally("ftp://ftp.example.org"));
    }

    public function testToDataUri() {
        $this->assertNull(File::toDataUri("gibtsnicht.txt"));

        $expected1 = file_get_contents(dirname(__file__) . "/fixtures/logo-data-url.txt");
        $this->assertEquals($expected1, File::toDataUri(Path::resolve("ULICMS_ROOT/admin/gfx/logo.png")));

        $expected2 = file_get_contents(dirname(__file__) . "/fixtures/hello-base64.txt");
        $this->assertEquals($expected2,
                File::toDataUri(Path::resolve(dirname(__file__) . "/fixtures/hello-original.txt"), "application/inf"));
    }

}
