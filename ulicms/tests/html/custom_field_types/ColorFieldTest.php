<?php

class ColorFieldTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new ColorField ();
        $field->name = "my_field";
        $field->title = "design";
        $rendered = $field->render("FFC0CB");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/color_field.expected.txt");

        // file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals(normalizeLN($expected), normalizeLN($rendered));
    }

}
