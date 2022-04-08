<?php

use Spatie\Snapshots\MatchesSnapshots;
use UliCMS\Fields\NumberField;

class NumberFieldTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new NumberField();
        $field->name = "my_field";
        $field->title = "users";
        $this->assertMatchesHtmlSnapshot($field->render("hello world"));
    }

}
