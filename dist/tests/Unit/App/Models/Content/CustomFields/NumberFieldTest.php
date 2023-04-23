<?php

use App\Models\Content\CustomFields\NumberField;
use Spatie\Snapshots\MatchesSnapshots;

class NumberFieldTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath('en');
    }

    public function testRender(): void {
        $field = new NumberField();
        $field->name = 'my_field';
        $field->title = 'users';
        $this->assertMatchesHtmlSnapshot($field->render('hello world'));
    }
}
