<?php

use App\Models\Content\CustomFields\FileImage;
use Spatie\Snapshots\MatchesSnapshots;

class FileImageTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        include_once getLanguageFilePath('en');
    }

    public function testRender()
    {
        $field = new FileImage();
        $field->name = 'my_field';
        $field->title = 'file';
        $this->assertMatchesHtmlSnapshot($field->render('/foo/bar/test.jpg'));
    }
}
