<?php

use App\Models\Content\CustomFields\UrlField;
use Spatie\Snapshots\MatchesSnapshots;

class UrlFieldTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        include_once getLanguageFilePath('en');
    }

    public function testRender()
    {
        $field = new UrlField();
        $field->name = 'my_field';
        $field->title = 'username';

        $this->assertMatchesHtmlSnapshot(
            $field->render('https://www.ulicms.de')
        );
    }
}
