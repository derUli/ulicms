<?php

class StringHelperTest extends \PHPUnit\Framework\TestCase
{
    private function getTestFilePath()
    {
        return Path::resolve("ULICMS_ROOT/tests/fixtures/lines.txt");
    }

    public function testRemoveEmptyLineFromString()
    {
        $input = file_get_contents(
            Path::resolve("ULICMS_ROOT/tests/fixtures/removeEmptyLinesFromString.input.txt")
        );
        $expected = normalizeLN(file_get_contents(Path::resolve("ULICMS_ROOT/tests/fixtures/removeEmptyLinesFromString.expected.txt")), "\n");

        $this->assertEquals(
            $expected,
            \App\Helpers\StringHelper::removeEmptyLinesFromString($input)
        );
    }

    public function testlinesFromFile()
    {
        $lines = \App\Helpers\StringHelper::linesFromFile(
            $this->getTestFilePath(),
            false,
            false,
            false
        );
        $this->assertCount(9, $lines);
        $this->assertFalse(str_starts_with($lines[2], " "));
        $this->assertTrue(str_ends_with($lines[2], " "));
        $this->assertTrue(str_starts_with($lines[3], " "));
        $this->assertTrue(str_ends_with($lines[3], " "));
        $this->assertEquals(17, strlen($lines[2]));
        $this->assertEquals(23, strlen($lines[3]));
    }

    public function testLinesFromFileRemoveEmpty()
    {
        $lines = \App\Helpers\StringHelper::linesFromFile($this->getTestFilePath(), false, true, false);
        $this->assertCount(5, $lines);
    }

    public function testLinesFromFileRemoveComments()
    {
        $lines = \App\Helpers\StringHelper::linesFromFile($this->getTestFilePath(), false, false, true);
        $this->assertCount(7, $lines);
    }

    public function testLinesFromFileRemoveCommentsAndEmpty()
    {
        $lines = \App\Helpers\StringHelper::linesFromFile($this->getTestFilePath(), false, true, true);
        $this->assertCount(3, $lines);
    }

    public function testLinesFromFileTrim()
    {
        $lines = \App\Helpers\StringHelper::linesFromFile($this->getTestFilePath(), true, false, false);
        $this->assertCount(9, $lines);
        $this->assertFalse(str_starts_with($lines[2], " "));
        $this->assertFalse(str_ends_with($lines[2], " "));
        $this->assertFalse(str_starts_with($lines[3], " "));
        $this->assertFalse(str_ends_with($lines[3], " "));
        $this->assertEquals(16, strlen($lines[2]));
        $this->assertEquals(21, strlen($lines[3]));
    }

    public function testLinesFromFileTrimRemoveCommentsAndEmpty()
    {
        $lines = \App\Helpers\StringHelper::linesFromFile($this->getTestFilePath(), true, true, true);
        $this->assertCount(3, $lines);
        $this->assertFalse(str_starts_with($lines[0], " "));
        $this->assertFalse(str_ends_with($lines[0], " "));
        $this->assertFalse(str_starts_with($lines[1], " "));
        $this->assertFalse(str_ends_with($lines[1], " "));

        $this->assertEquals(16, strlen($lines[0]));
        $this->assertEquals(21, strlen($lines[1]));
    }

    public function testLinesFromFileNotFound()
    {
        $lines = \App\Helpers\StringHelper::linesFromFile("path/this-is-not-a-file", true, true, true);
        $this->assertNull($lines);
    }

    public function testTrimLines()
    {
        $inputFile = Path::resolve("ULICMS_ROOT/tests/fixtures/trimLines.input.txt");
        $inputExpected = Path::resolve("ULICMS_ROOT/tests/fixtures/trimLines.expected.txt");

        $input = normalizeLN(file_get_contents($inputFile));
        $expected = normalizeLN(file_get_contents($inputExpected));
        $this->assertEquals($expected, \App\Helpers\StringHelper::trimLines($input));
    }

    public function testMakeLinksClickable()
    {
        $input = "Das hier ist ein Text.
http://www.google.de
Noch mehr Text http://www.ulicms.de und so weiter.";

        $expected = 'Das hier ist ein Text.
<a href="http://www.google.de" rel="nofollow" target="_blank">http://www.google.de</a>
Noch mehr Text <a href="http://www.ulicms.de" rel="nofollow" target="_blank">http://www.ulicms.de</a> und so weiter.';

        $this->assertEquals($expected, \App\Helpers\StringHelper::makeLinksClickable($input));
    }

    public function testCleanString()
    {
        $this->assertEquals("hello-world", \App\Helpers\StringHelper::cleanString("Hello World"));
        $this->assertEquals("hello-world", \App\Helpers\StringHelper::cleanString("Hello World", "-"));
        $this->assertEquals("hello_world", \App\Helpers\StringHelper::cleanString("Hello World", "_"));
        $this->assertEquals("das-ist-die-grossfraesmaschinenoeffnungstuer", \App\Helpers\StringHelper::cleanString("Das ist die Großfräsmaschinenöffnungstür."));
    }

    public function testIsUpperCaseReturnsTrue()
    {
        $this->assertTrue(\App\Helpers\StringHelper::isUpperCase("SEHR SEHR GROSS"));
    }

    public function testIsUpperCaseReturnsFalse()
    {
        $this->assertFalse(\App\Helpers\StringHelper::isUpperCase("Gemischter Case"));
    }

    public function testIsLowerCaseReturnsTrue()
    {
        $this->assertTrue(\App\Helpers\StringHelper::isLowerCase("sehr sehr klein"));
    }

    public function testIsLowerCaseReturnsFalse()
    {
        $this->assertFalse(\App\Helpers\StringHelper::isLowerCase("Das ist Nicht Lowercase"));
    }

    public function testGetExcerptReturnsShortedString()
    {
        $this->assertEquals(
            "Lorem Ipsum...",
            \App\Helpers\StringHelper::getExcerpt(
                "Lorem Ipsum sit dor amet usw.",
                0,
                16
            )
        );
    }

    public function testGetExcerptReturnsFullString()
    {
        $this->assertEquals(
            "Lorem Ipsum sit dor amet usw.",
            \App\Helpers\StringHelper::getExcerpt(
                "Lorem Ipsum sit dor amet usw.",
                0,
                100
            )
        );
    }

    public function testRealHtmlSpecialChars()
    {
        $this->assertEquals(
            "&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;",
            \App\Helpers\StringHelper::realHtmlSpecialchars("<script>alert('xss')</script>")
        );
    }

    public function testKeywordsFromString()
    {
        $input = file_get_contents(
            Path::resolve("ULICMS_ROOT/tests/fixtures/lorem_ipsum.txt")
        );

        $keywords = \App\Helpers\StringHelper::keywordsFromString($input);
        $this->assertCount(74, $keywords);
        $this->assertEquals(7, $keywords["Lorem"]);
        $this->assertEquals(14, $keywords["et"]);
        $this->assertEquals(7, $keywords["ipsum"]);
        $this->assertEquals(5, $keywords["dolore"]);
    }
}
