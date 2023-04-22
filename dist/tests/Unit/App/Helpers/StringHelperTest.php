<?php

use App\Helpers\StringHelper;

use Spatie\Snapshots\MatchesSnapshots;

class StringHelperTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    public function testRemoveEmptyLineFromString()
    {
        $input = file_get_contents(
            Path::resolve('ULICMS_ROOT/tests/fixtures/removeEmptyLinesFromString.input.txt')
        );
        $expected = normalizeLN(file_get_contents(Path::resolve('ULICMS_ROOT/tests/fixtures/removeEmptyLinesFromString.expected.txt')), "\n");

        $this->assertEquals(
            $expected,
            StringHelper::removeEmptyLinesFromString($input)
        );
    }

    public function testlinesFromFile()
    {
        $lines = StringHelper::linesFromFile(
            $this->getTestFilePath(),
            false,
            false,
            false
        );
        $this->assertCount(9, $lines);
        $this->assertFalse(str_starts_with($lines[2], ' '));
        $this->assertTrue(str_ends_with($lines[2], ' '));
        $this->assertTrue(str_starts_with($lines[3], ' '));
        $this->assertTrue(str_ends_with($lines[3], ' '));
        $this->assertEquals(17, strlen($lines[2]));
        $this->assertEquals(23, strlen($lines[3]));
    }

    public function testLinesFromFileRemoveEmpty()
    {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), false, true, false);
        $this->assertCount(5, $lines);
    }

    public function testLinesFromFileRemoveComments()
    {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), false, false, true);
        $this->assertCount(7, $lines);
    }

    public function testLinesFromFileRemoveCommentsAndEmpty()
    {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), false, true, true);
        $this->assertCount(3, $lines);
    }

    public function testLinesFromFileTrim()
    {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), true, false, false);
        $this->assertCount(9, $lines);
        $this->assertFalse(str_starts_with($lines[2], ' '));
        $this->assertFalse(str_ends_with($lines[2], ' '));
        $this->assertFalse(str_starts_with($lines[3], ' '));
        $this->assertFalse(str_ends_with($lines[3], ' '));
        $this->assertEquals(16, strlen($lines[2]));
        $this->assertEquals(21, strlen($lines[3]));
    }

    public function testLinesFromFileTrimRemoveCommentsAndEmpty()
    {
        $lines = StringHelper::linesFromFile($this->getTestFilePath(), true, true, true);
        $this->assertCount(3, $lines);
        $this->assertFalse(str_starts_with($lines[0], ' '));
        $this->assertFalse(str_ends_with($lines[0], ' '));
        $this->assertFalse(str_starts_with($lines[1], ' '));
        $this->assertFalse(str_ends_with($lines[1], ' '));

        $this->assertEquals(16, strlen($lines[0]));
        $this->assertEquals(21, strlen($lines[1]));
    }

    public function testLinesFromFileNotFound()
    {
        $lines = StringHelper::linesFromFile('path/this-is-not-a-file', true, true, true);
        $this->assertNull($lines);
    }

    public function testTrimLines()
    {
        $inputFile = Path::resolve('ULICMS_ROOT/tests/fixtures/trimLines.input.txt');

        $actual = StringHelper::trimLines(file_get_contents($inputFile));

        $this->assertMatchesTextSnapshot($actual);
    }

    public function testMakeLinksClickable()
    {
        $input = 'Das hier ist ein Text.
http://www.google.de
Noch mehr Text http://www.ulicms.de und so weiter.';

        $expected = 'Das hier ist ein Text.
<a href="http://www.google.de" rel="nofollow" target="_blank">http://www.google.de</a>
Noch mehr Text <a href="http://www.ulicms.de" rel="nofollow" target="_blank">http://www.ulicms.de</a> und so weiter.';

        $this->assertEquals($expected, StringHelper::makeLinksClickable($input));
    }

    public function testCleanString()
    {
        $this->assertEquals('hello-world', StringHelper::cleanString('Hello World'));
        $this->assertEquals('das-ist-die-grossfraesmaschinenoeffnungstuer', StringHelper::cleanString('Das ist die Großfräsmaschinenöffnungstür.'));
    }

    public function testGetExcerptReturnsShortedString()
    {
        $this->assertEquals(
            'Lorem Ipsum...',
            StringHelper::getExcerpt(
                'Lorem Ipsum sit dor amet usw.',
                0,
                16
            )
        );
    }

    public function testGetExcerptReturnsFullString()
    {
        $this->assertEquals(
            'Lorem Ipsum sit dor amet usw.',
            StringHelper::getExcerpt(
                'Lorem Ipsum sit dor amet usw.',
                0,
                100
            )
        );
    }

    public function testSplitAndTrim()
    {
        $input = 'Max;
        Muster;
        max@muster.de;
        Musterstadt';
        $result = StringHelper::splitAndTrim($input);
        $this->assertEquals('Max', $result[0]);
        $this->assertEquals('Muster', $result[1]);
        $this->assertEquals('max@muster.de', $result[2]);
        $this->assertEquals('Musterstadt', $result[3]);
    }

    private function getTestFilePath()
    {
        return Path::resolve('ULICMS_ROOT/tests/fixtures/lines.txt');
    }
}