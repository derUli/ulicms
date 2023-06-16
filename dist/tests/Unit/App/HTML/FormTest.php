<?php

use App\HTML\Form;
use App\Translations\Translation;

class FormTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        parent::setUp();

        require_once getLanguageFilePath('en');
        Translation::loadAllModuleLanguageFiles('en');
    }

    public function testBuildMethodCallForm(): void {
        $actual = Form::buildMethodCallForm('FooClass', 'FooMethod');

        $this->assertStringContainsString('<form action="index.php" method="post">', $actual);
        $this->assertStringContainsString('<input type="hidden" name="sClass" value="FooClass">', $actual);
        $this->assertStringContainsString('<input type="hidden" name="sMethod" value="FooMethod">', $actual);
    }

    public function testBuildMethodCallFormWithHtmlAttributes(): void {
        $actual = Form::buildMethodCallForm('FooClass', 'FooMethod', htmlAttributes: ['foo' => 'bar']);

        $this->assertStringContainsString('<form action="index.php" method="post" foo="bar">', $actual);
    }

    public function testBuildMethodCallButton(): void {
        $actual = Form::buildMethodCallButton('FooClass', 'FooMethod', 'Hello World');

        $this->assertStringContainsString('<form action="index.php" method="post">', $actual);
        $this->assertStringContainsString('<input type="hidden" name="sClass" value="FooClass">', $actual);
        $this->assertStringContainsString('<input type="hidden" name="sMethod" value="FooMethod">', $actual);
        $this->assertStringContainsString('<button class="btn btn-light" type="submit">Hello World</button>', $actual);
    }

    public function testEndform(): void {
        $this->assertEquals('</form>', Form::endForm());
    }

    public function testDeleteButton(): void {
        $actual = Form::deleteButton('https://example.org', ['foo' => 'bar']);

        $this->assertStringContainsString('<form action="https://example.org" method="post" class="delete-form">', $actual);
        $this->assertStringContainsString('<input type="hidden" name="foo" value="bar" />', $actual);
        $this->assertStringContainsString('<input type="image" src="admin/gfx/delete.png" alt="Delete" title="Delete" />', $actual);
    }
}
