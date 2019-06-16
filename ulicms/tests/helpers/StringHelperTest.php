<?php

class StringHelperTest extends \PHPUnit\Framework\TestCase {

    private function getTestFilePath() {
        return Path::resolve("ULICMS_ROOT/tests/fixtures/lines.txt");
    }

    public function testRemoveEmptyLineFromString() {
        $input = file_get_contents(
                Path::resolve("ULICMS_ROOT/tests/fixtures/removeEmptyLinesFromString.input.txt"));
        $expected = normalizeLN(file_get_contents(Path::resolve("ULICMS_ROOT/tests/fixtures/removeEmptyLinesFromString.expected.txt")), "\n");

        $this->assertEquals($expected,
                StringHelper::removeEmptyLinesFromString($input));
    }

    public function testlinesFromFile() {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), false,
                        false, false);
        $this->assertCount(9, $lines);
        $this->assertFalse(startsWith($lines[2], " "));
        $this->assertTrue(endsWith($lines[2], " "));
        $this->assertTrue(startsWith($lines[3], " "));
        $this->assertTrue(endsWith($lines[3], " "));
        $this->assertEquals(17, strlen($lines[2]));
        $this->assertEquals(23, strlen($lines[3]));
    }

    public function testLinesFromFileRemoveEmpty() {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), false, true, false);
        $this->assertCount(5, $lines);
    }

    public function testLinesFromFileRemoveComments() {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), false, false, true);
        $this->assertCount(7, $lines);
    }

    public function testLinesFromFileRemoveCommentsAndEmpty() {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), false, true, true);
        $this->assertCount(3, $lines);
    }

    public function testLinesFromFileTrim() {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), true, false, false);
        $this->assertCount(9, $lines);
        $this->assertFalse(startsWith($lines[2], " "));
        $this->assertFalse(endsWith($lines[2], " "));
        $this->assertFalse(startsWith($lines[3], " "));
        $this->assertFalse(endsWith($lines[3], " "));
        $this->assertEquals(16, strlen($lines[2]));
        $this->assertEquals(21, strlen($lines[3]));
    }

    public function testLinesFromFileTrimRemoveCommentsAndEmpty() {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), true, true, true);
        $this->assertCount(3, $lines);
        $this->assertFalse(startsWith($lines[0], " "));
        $this->assertFalse(endsWith($lines[0], " "));
        $this->assertFalse(startsWith($lines[1], " "));
        $this->assertFalse(endsWith($lines[1], " "));

        $this->assertEquals(16, strlen($lines[0]));
        $this->assertEquals(21, strlen($lines[1]));
    }

    public function testLinesFromFileNotFound() {
        $lines = StringHelper::linesFromFile("path/this-is-not-a-file", true, true, true);
        $this->assertNull($lines);
    }

    public function testTrimLines() {
        $inputFile = Path::resolve("ULICMS_ROOT/tests/fixtures/trimLines.input.txt");
        $inputExpected = Path::resolve("ULICMS_ROOT/tests/fixtures/trimLines.expected.txt");

        $input = file_get_contents($inputFile);
        $expected = file_get_contents($inputExpected);
        $this->assertEquals($expected, StringHelper::trimLines($input));
    }

    public function testMakeLinksClickable() {
        $input = "Das hier ist ein Text.
http://www.google.de
Noch mehr Text http://www.ulicms.de und so weiter.";

        $expected = 'Das hier ist ein Text.
<a href="http://www.google.de" rel="nofollow" target="_blank">http://www.google.de</a>
Noch mehr Text <a href="http://www.ulicms.de" rel="nofollow" target="_blank">http://www.ulicms.de</a> und so weiter.';

        $this->assertEquals($expected, StringHelper::makeLinksClickable($input));
    }

    public function testCleanString() {
        $this->assertEquals("hello-world", StringHelper::cleanString("Hello World"));
        $this->assertEquals("hello-world", StringHelper::cleanString("Hello World", "-"));
        $this->assertEquals("hello_world", StringHelper::cleanString("Hello World", "_"));
        $this->assertEquals("das-ist-die-grossfraesmaschinenoeffnungstuer", StringHelper::cleanString("Das ist die Großfräsmaschinenöffnungstür."));
    }

}
