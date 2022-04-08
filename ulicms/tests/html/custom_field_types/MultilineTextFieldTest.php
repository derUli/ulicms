<?php

use Spatie\Snapshots\MatchesSnapshots;
use UliCMS\Fields\MultilineTextField;

class MultilineTextFieldTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $field = new MultilineTextField();
        $field->name = "my_field";
        $field->title = "users";
        $this->assertMatchesHtmlSnapshot($field->render(123));
    }

}
