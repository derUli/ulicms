<?php

use Spatie\Snapshots\MatchesSnapshots;
use UliCMS\Fields\FileImage;

class FileImageTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new FileImage();
        $field->name = "my_field";
        $field->title = "file";
        $this->assertMatchesHtmlSnapshot($field->render("/foo/bar/test.jpg"));
    }

}
