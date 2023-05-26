<?php

use App\Models\Content\CustomFields\TextField;
use Spatie\Snapshots\MatchesSnapshots;

class TextFieldTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath('en');
    }

    public function testRender(): void {
        $field = new TextField();
        $field->name = 'my_field';
        $field->title = 'username';
        $this->assertMatchesHtmlSnapshot(
            $field->render('hello world')
        );
    }
}
