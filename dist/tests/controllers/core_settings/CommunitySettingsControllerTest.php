<?php

class CommunitySettingsControllerTest extends \PHPUnit\Framework\TestCase
{
    private $defaultSettings = [];

    protected function setUp(): void
    {
        $this->defaultSettings = [
            "comments_enabled " => Settings::get("comments_enabled"),
            "comments_must_be_approved" => Settings::get("comments_must_be_approved"),
            "commentable_content_types" => Settings::get("commentable_content_types")
        ];
    }

    protected function tearDown(): void
    {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePostShouldSave(): void
    {
        $_POST["comments_enabled"] = "1";
        $_POST["comments_must_be_approved"] = "1";
        $_POST["commentable_content_types"] = ["page", "article"];

        Settings::set("default_language", 'en');

        $controller = new CommunitySettingsController();
        $controller->_savePost();

        $this->assertEquals(
            "1",
            Settings::get('comments_enabled')
        );

        $this->assertEquals(
            "1",
            Settings::get('comments_must_be_approved')
        );
        $this->assertEquals(
            "page;article",
            Settings::get('commentable_content_types')
        );
    }

    public function testSavePostShoulDelete(): void
    {
        Settings::set("default_language", 'en');

        $controller = new CommunitySettingsController();
        $controller->_savePost();

        $this->assertNull(Settings::get('comments_enabled'));
        $this->assertNull(Settings::get('comments_must_be_approved'));
        $this->assertNull(Settings::get('commentable_content_types'));
    }
}
