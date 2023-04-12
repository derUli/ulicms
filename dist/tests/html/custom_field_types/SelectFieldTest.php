<?php

use Spatie\Snapshots\MatchesSnapshots;

class SelectFieldTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    public function testRenderSingleSelectField()
    {
        $field = new SelectField();
        $field->name = 'zip_codes';
        $field->title = 'zip_codes';
        $field->helpText = 'hold_ctrl_to_select_multiple';
        $field->translateOptions = false;
        $field->options = $this->getOptions();
        $this->assertMatchesHtmlSnapshot($field->render());
    }

    public function testRenderMultiSelectField()
    {
        $field = new MultiSelectField();
        $field->name = 'zip_codes';
        $field->title = 'zip_codes';
        $field->helpText = 'hold_ctrl_to_select_multiple';
        $field->translateOptions = false;
        $field->options = $this->getOptions();
        $this->assertMatchesHtmlSnapshot($field->render());
    }

    private function getOptions(): array
    {
        return [
            '38102' => 'Braunschweig',
            '38104' => 'Gliesmarode',
            '38124' => 'Heidburg',
            '38100' => 'Innenstadt',
            '38116' => 'Kanzlerfeld'
        ];
    }
}
