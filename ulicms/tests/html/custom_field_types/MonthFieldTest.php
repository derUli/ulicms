<?php

use Spatie\Snapshots\MatchesSnapshots;

class MonthFieldTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        include_once getLanguageFilePath("en");
    }

    public function testRender()
    {
        $field = new MonthField();
        $field->name = "my_field";
        $field->title = "username";
        $this->assertMatchesHtmlSnapshot($field->render("2019-04"));
    }
}
