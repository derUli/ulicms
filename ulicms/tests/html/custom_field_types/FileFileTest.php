<?php

use Spatie\Snapshots\MatchesSnapshots;

class FileFileTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        include_once getLanguageFilePath("en");
    }

    public function testRender()
    {
        $field = new FileFile();
        $field->name = "my_field";
        $field->title = "file";
        $this->assertMatchesHtmlSnapshot($field->render("/foo/bar/test.pdf"));
    }
}
