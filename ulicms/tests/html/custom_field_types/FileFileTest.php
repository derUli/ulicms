<?php

class FileFileTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new FileFile();
        $field->name = "my_field";
        $field->title = "file";
        $rendered = $field->render("/foo/bar/test.pdf");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/file_file.expected.txt");

        // file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }

}
