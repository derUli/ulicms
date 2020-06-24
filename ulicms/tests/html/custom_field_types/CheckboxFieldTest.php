<?php

class CheckboxFieldTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new CheckboxField();
        $field->name = "my_field";
        $field->title = "enabld";
        $rendered = $field->render(true);

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/checkbox.expected.txt");

        //file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }

}
