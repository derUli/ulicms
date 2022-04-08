<?php

use Spatie\Snapshots\MatchesSnapshots;
use UliCMS\Fields\ColorField;

class ColorFieldTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new ColorField();
        $field->name = "my_field";
        $field->title = "design";

        $this->assertMatchesHtmlSnapshot($field->render("FFC0CB"));
    }

}
