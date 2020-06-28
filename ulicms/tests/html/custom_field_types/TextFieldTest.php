<?php

class TextFieldTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        include_once getLanguageFilePath("en");
    }

    public function testRender()
    {
        $field = new TextField();
        $field->name = "my_field";
        $field->title = "username";
        $rendered = $field->render("hello world");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/textfield.expected.txt");

        //file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }
}
