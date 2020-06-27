<?php

class SelectFieldTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderSingleSelectField()
    {
        $field = new SelectField();
        $field->name = "zip_codes";
        $field->title = "zip_codes";
        $field->helpText = "hold_ctrl_to_select_multiple";
        $field->translateOptions = false;
        $field->options = $this->getOptions();
        $html = $field->render();

        $expected = file_get_contents(
            Path::resolve(
                    "ULICMS_ROOT/tests/fixtures/custom_field_types/select_field.expected.txt"
                )
        );

        $this->assertEquals(
            normalizeLN($expected),
            normalizeLN($html)
        );
    }

    public function testRenderMultiSelectField()
    {
        $field = new MultiSelectField();
        $field->name = "zip_codes";
        $field->title = "zip_codes";
        $field->helpText = "hold_ctrl_to_select_multiple";
        $field->translateOptions = false;
        $field->options = $this->getOptions();
        $html = $field->render();

        $expected = file_get_contents(
            Path::resolve(
                    "ULICMS_ROOT/tests/fixtures/custom_field_types/multi_select_field.expected.txt"
                )
        );

        $this->assertEquals(
            normalizeLN($expected),
            normalizeLN($html)
        );
    }

    private function getOptions(): array
    {
        return [
            "38102" => "Braunschweig",
            "38104" => "Gliesmarode",
            "38124" => "Heidburg",
            "38100" => "Innenstadt",
            "38116" => "Kanzlerfeld"
        ];
    }
}
