<?php

use Spatie\Snapshots\MatchesSnapshots;

class CheckboxFieldTest extends \PHPUnit\Framework\TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        include_once getLanguageFilePath('en');
    }

    public function testRender()
    {
        $field = new CheckboxField();
        $field->name = 'my_field';
        $field->title = 'enabld';

        $this->assertMatchesHtmlSnapshot($field->render(true));
    }
}
