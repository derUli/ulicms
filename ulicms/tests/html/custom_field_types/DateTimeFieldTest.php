<?php

class DateTimeFieldTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new DatetimeField();
        $field->name = "my_field";
        $field->title = "date";
        $rendered = $field->render("1996-12-19T16:39:57-08:00");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/datetime.expected.txt");

        // file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }

}
