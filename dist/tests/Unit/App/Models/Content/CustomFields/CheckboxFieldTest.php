<?php

use App\Models\Content\CustomFields\CheckboxField;
use Spatie\Snapshots\MatchesSnapshots;

class CheckboxFieldTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath('en');
    }

    public function testRender(): void {
        $field = new CheckboxField();
        $field->name = 'my_field';
        $field->title = 'enabld';

        $this->assertMatchesHtmlSnapshot($field->render(true));
    }
}
