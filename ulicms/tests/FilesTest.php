<?php

class FilesTest extends \PHPUnit\Framework\TestCase {

    public function testFileExtension() {
        $this->assertEquals("pdf", file_extension("myfile.pdf"));
        $this->assertEquals("pdf", file_extension("myfile.PDF"));
        $this->assertEquals("txt", file_extension("foo.txt"));
        $this->assertEquals("myfile", file_extension("myfile"));
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

}
