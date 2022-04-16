<?php

use Spatie\Snapshots\MatchesSnapshots;
use UliCMS\Models\Content\CustomData;

class MediaEmbedTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        $_SESSION = [
            "language" => "de"
        ];
    }

    protected function tearDown(): void {
        $_SESSION = [];
    }

    public function testReplaceLinks() {
        $input = file_get_contents("tests/fixtures/AutoEmbed/input.html");
        $actual = apply_filter($input, "before_content");

        $this->assertMatchesHtmlSnapshot($actual);
    }

    public function testReplaceLinksWithDisableMediaEmbedTrue() {
        $page = $this->getTestPage();

        set_requested_pagename($page->slug, $page->language);
        CustomData::set("disable_media_embed", true);

        $input = file_get_contents("tests/fixtures/AutoEmbed/input.html");
        $actual = apply_filter($input, "before_content");

        $this->assertMatchesHtmlSnapshot($actual);

        $page->delete();
    }

    public function testReplaceLinksWithDisableMediaEmbedFalse() {
        $page = $this->getTestPage();

        set_requested_pagename($page->slug, $page->language);
        CustomData::set("disable_media_embed", false);

        $input = file_get_contents("tests/fixtures/AutoEmbed/input.html");
        $actual = apply_filter($input, "before_content");

        $this->assertMatchesHtmlSnapshot($actual);

        $page->delete();
    }

    private function getTestPage() {
        $page = new Page();
        $page->title = 'Test Page';
        $page->slug = uniqid();
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->author_id = 1;
        $page->show_headline = true;
        $page->save();

        return $page;
    }

}
