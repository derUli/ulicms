<?php

class FileImageTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        include_once getLanguageFilePath("en");
    }

    public function testRender()
    {
        $field = new FileImage();
        $field->name = "my_field";
        $field->title = "file";
        $rendered = $field->render("/foo/bar/test.jpg");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/file_image.expected.txt");

        // file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }
}
