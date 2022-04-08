<?php

use Spatie\Snapshots\MatchesSnapshots;
use UliCMS\Fields\DatetimeField;

class DateTimeFieldTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new DatetimeField();
        $field->name = "my_field";
        $field->title = "date";
        $this->assertMatchesHtmlSnapshot(
                $field->render("2020-05-17 11:51")
        );
    }

}
