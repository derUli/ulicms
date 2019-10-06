<?php

use UliCMS\Packages\Patch;

class PatchTest extends \PHPUnit\Framework\TestCase {

    public function testConstructor() {
        $patch = new Patch(
                "foobar",
                "This is a patch",
                "https://example.org/foobar.tar.gz",
                "90df380195f530f4ef72d9b73fe4708e"
        );

        $this->assertEquals("foobar", $patch->name);
        $this->assertEquals("This is a patch", $patch->description);
        $this->assertEquals("https://example.org/foobar.tar.gz", $patch->url);
        $this->assertEquals("90df380195f530f4ef72d9b73fe4708e", $patch->hash);
    }

    public function testToLine() {
        $patch = new Patch(
                "foobar",
                "This is a patch",
                "https://example.org/foobar.tar.gz",
                "90df380195f530f4ef72d9b73fe4708e"
        );

        $this->assertEquals(
                "foobar|"
                . "This is a patch|"
                . "https://example.org/foobar.tar.gz|"
                . "90df380195f530f4ef72d9b73fe4708e",
                $patch->toLine()
        );
    }

    public function testFromLine() {
        $patch = Patch::fromLine(
                        "foobar|"
                        . "This is a patch|"
                        . "https://example.org/foobar.tar.gz|"
                        . "90df380195f530f4ef72d9b73fe4708e"
        );

        $this->assertEquals("foobar", $patch->name);
        $this->assertEquals("This is a patch", $patch->description);
        $this->assertEquals("https://example.org/foobar.tar.gz", $patch->url);
        $this->assertEquals("90df380195f530f4ef72d9b73fe4708e", $patch->hash);
    }

    public function testInstallRetursFalse() {
        $patch = new Patch(
                "foobar",
                "This is a patch",
                "https://example.org/foobar.tar.gz",
                "90df380195f530f4ef72d9b73fe4708e"
        );
        $this->assertFalse($patch->install());
    }

}
