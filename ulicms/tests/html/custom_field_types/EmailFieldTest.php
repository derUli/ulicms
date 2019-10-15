<?php

class EmailFieldTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new EmailField ();
        $field->name = "my_field";
        $field->title = "email";
        $rendered = $field->render("foo@bar.de");

        $expectedFile = Path::resolve("ULICMS_ROOT/tests/fixtures/custom_field_types/email_field.expected.txt");

        //file_put_contents($expectedFile, $rendered);

        $expected = file_get_contents($expectedFile);
        $this->assertEquals($expected, $rendered);
    }

}
