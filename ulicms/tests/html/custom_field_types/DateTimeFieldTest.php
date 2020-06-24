<?php

class DateTimeFieldTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new DatetimeField();
        $field->name = "my_field";
        $field->title = "date";
        $rendered = $field->render("2020-05-17 11:51");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/datetime.expected.txt");

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }

}
