<?php

use App\Models\Content\CustomFields\EmailField;
use Spatie\Snapshots\MatchesSnapshots;

class EmailFieldTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        include_once getLanguageFilePath('en');
    }

    public function testRender()
    {
        $field = new EmailField();
        $field->name = 'my_field';
        $field->title = 'email';
        $this->assertMatchesHtmlSnapshot($field->render('foo@bar.de'));
    }
}
