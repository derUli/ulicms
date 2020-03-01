<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MediaEmbedTest
 *
 * @author deruli
 */
class MediaEmbedTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_SESSION = [
            "language" => "de"
        ];
    }

    public function tearDown() {
        $_SESSION = [];
    }

    public function testReplaceLinks() {
        $input = file_get_contents("tests/fixtures/AutoEmbed/input.html");
        $expected = file_get_contents("tests/fixtures/AutoEmbed/expected.html");
        $output = apply_filter($input, "before_content");

        $this->assertEquals(normalizeLN($expected), normalizeLN($output));
    }

    public function testReplaceLinksWithDisableMediaEmbedTrue() {
        $page = $this->getTestPage();

        set_requested_pagename($page->slug, $page->language);
        CustomData::set("disable_media_embed", true);

        $input = file_get_contents("tests/fixtures/AutoEmbed/input.html");
        $output = apply_filter($input, "before_content");

        $this->assertEquals(normalizeLN($input), normalizeLN($output));

        $page->delete();
    }

    public function testReplaceLinksWithDisableMediaEmbedFalse() {
        $page = $this->getTestPage();

        set_requested_pagename($page->slug, $page->language);
        CustomData::set("disable_media_embed", false);

        $input = file_get_contents("tests/fixtures/AutoEmbed/input.html");
        $expected = file_get_contents("tests/fixtures/AutoEmbed/expected.html");
        $output = apply_filter($input, "before_content");

        $this->assertEquals(normalizeLN($expected), normalizeLN($output));

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
