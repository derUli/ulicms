<?php

class MultilineTextFieldTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        include_once getLanguageFilePath("en");
    }

    public function testRender()
    {
        $field = new MultilineTextField();
        $field->name = "my_field";
        $field->title = "users";
        $rendered = $field->render(123);

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/textfield_multiline.expected.txt");

        //file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }
}
