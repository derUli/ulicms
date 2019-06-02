<?php

class TemplatingTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        $this->cleanUp();
    }

    private function cleanUp() {
        Vars::delete("page");
        Vars::delete("type");

        unset($_GET ["seite"]);
        Database::query("delete from {prefix}content where slug = 'testdisableshortcodes' or title like 'Unit Test%'", true);
    }

    public function testGetRequestedPageNameWithSlugSet() {
        $_GET ["seite"] = "foobar";
        $this->assertEquals("foobar", get_requested_pagename());
        $this->cleanUp();
    }

    public function testGetRequestedPageNameWithoutSlug() {
        $this->cleanUp();
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testGetRequestedPageNameWithNull() {
        $_GET ["seite"] = null;
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testGetRequestedPageNameWithEmptyString() {
        $_GET ["seite"] = "";
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testIsHomeTrue() {
        $_GET ["seite"] = get_frontpage();
        $this->assertTrue(is_home());
        $this->cleanUp();
    }

    public function testIsHomeFalse() {
        $_GET ["seite"] = "nothome";
        $this->assertFalse(is_home());
        $this->cleanUp();
    }

    public function testIsFrontPageTrue() {
        $_GET ["seite"] = get_frontpage();
        $this->assertTrue(is_frontpage());
        $this->cleanUp();
    }

    public function testIsFrontPageFalse() {
        $_GET ["seite"] = "nothome";
        $this->assertFalse(is_frontpage());
        $this->cleanUp();
    }

    public function testGetType() {
        $content1 = new Module_Page();
        $content1->title = 'Unit Test ' . uniqid();
        $content1->slug = 'unit-test-' . uniqid();
        $content1->language = 'de';
        $content1->content = "even more text";
        $content1->comments_enabled = false;
        $content1->author_id = 1;
        $content1->group_id = 1;
        $content1->save();

        $this->assertEquals("module",
                get_type($content1->slug,
                        $content1->language));

        $content1->type = "video";
        $content1->save();

        // The type is cached so get_type() returns the same
        $this->assertEquals("module",
                get_type($content1->slug,
                        $content1->language));
        // unset the cached type
        Vars::delete("type_{$content1->slug}_{$content1->language}");

        // no it should get the actual type (video)
        $this->assertEquals("video",
                get_type($content1->slug,
                        $content1->language));

        $content2 = new Article();
        $content2->title = 'Unit Test ' . uniqid();
        $content2->slug = 'unit-test-' . uniqid();
        $content2->language = 'de';
        $content2->content = "even more text";
        $content2->comments_enabled = false;
        $content2->author_id = 1;
        $content2->group_id = 1;
        $content2->save();

        // the type is cached
        $this->assertEquals("article",
                get_type($content2->slug,
                        $content2->language));
    }

    public function testSetRequestedPageName() {
        set_requested_pagename("my-slug", "en", "pdf");
        $this->assertEquals("my-slug", get_requested_pagename());
        $this->assertEquals("en", Request::getVar("language"));
        $this->assertEquals("pdf", get_format());
    }

    public function testGetMenu() {
        $_SESSION["language"] = 'en';
        $html = get_menu("top", null, false);
        $this->assertStringContainsString("<ul", $html);
        $this->assertStringContainsString("<li", $html);
        $this->assertStringContainsString("menu_top", $html);
        $this->assertStringContainsString("<a href", $html);

        $pages = Contentfactory::getAllByMenuAndLanguage("top", "en");
        foreach ($pages as $page) {
            if (!$page->isFrontPage() && $page->isRegular() && !$page->getParent()) {
                $this->assertStringContainsString($page->slug . ".html", $html);
                $this->assertStringContainsString($page->title, $html);
            }
        }
        $germanPages = Contentfactory::getAllByLanguage("de");
        foreach ($germanPages as $page) {
            $this->assertStringNotContainsString($page->title . ".html", $html);
        }
    }

}
