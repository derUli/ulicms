<?php

use App\Models\Content\CustomFields\MonthField;
use Spatie\Snapshots\MatchesSnapshots;

class MonthFieldTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath('en');
    }

    public function testRender(): void {
        $field = new MonthField();
        $field->name = 'my_field';
        $field->title = 'username';
        $this->assertMatchesHtmlSnapshot($field->render('2019-04'));
    }
}
