<?php

class MonthFieldTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new MonthField();
        $field->name = "my_field";
        $field->title = "username";
        $rendered = $field->render("2019-04");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/monthfield.expected.txt");

        // file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }

}
