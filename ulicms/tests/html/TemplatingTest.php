<?php

class TemplatingTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_SESSION["language"] = "de";
        $_GET["slug"] = get_frontpage();
        require_once getLanguageFilePath("en");

        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = "80";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo.html";
    }

    public function tearDown() {
        $this->cleanUp();

        Settings::set("maintenance_mode", "off");

        unset($_SERVER["SERVER_PROTOCOL"]);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['REQUEST_URI']);
        unset($_GET["slug"]);
        unset($_SESSION["login_id"]);
        unset($_SESSION["language"]);
    }

    private function cleanUp() {
        Vars::delete("page");
        Vars::delete("type");

        Database::query("delete from {prefix}content where slug = 'testdisableshortcodes' or title like 'Unit Test%'", true);
    }

    public function testGetRequestedPageNameWithSlugSet() {
        $_GET ["slug"] = "foobar";
        $this->assertEquals("foobar", get_requested_pagename());
        $this->cleanUp();
    }

    public function testGetRequestedPageNameWithoutSlug() {
        $this->cleanUp();
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testGetRequestedPageNameWithNull() {
        $_GET ["slug"] = null;
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testGetRequestedPageNameWithEmptyString() {
        $_GET ["slug"] = "";
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testIsHomeTrue() {
        $_GET ["slug"] = get_frontpage();
        $this->assertTrue(is_home());
        $this->cleanUp();
    }

    public function testIsHomeFalse() {
        $_GET ["slug"] = "nothome";
        $this->assertFalse(is_home());
        $this->cleanUp();
    }

    public function testIsFrontPageTrue() {
        $_GET ["slug"] = get_frontpage();
        $this->assertTrue(is_frontpage());
        $this->cleanUp();
    }

    public function testIsFrontPageFalse() {
        $_GET ["slug"] = "nothome";
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

    public function testMenu() {
        $_SESSION["language"] = 'en';

        ob_start();
        menu("top", null, false);
        $html = ob_get_clean();

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

    public function testHtml5Doctype() {
        ob_start();
        html5_doctype();
        $this->assertEquals("<!doctype html>", ob_get_clean());
    }

    public function testOgHTMLPrefix() {
        $_SESSION["language"] = "en";

        ob_start();
        og_html_prefix();

        $this->assertEquals(
                "<html prefix=\"og: http://ogp.me/ns#\" lang=\"en\">",
                ob_get_clean()
        );
        $_SESSION["language"] = "de";

        ob_start();
        og_html_prefix();
        $this->assertEquals(
                "<html prefix=\"og: http://ogp.me/ns#\" lang=\"de\">",
                ob_get_clean()
        );
    }

    public function testGetOgHTMLPrefix() {
        $_SESSION["language"] = "en";
        $this->assertEquals(
                "<html prefix=\"og: http://ogp.me/ns#\" lang=\"en\">",
                get_og_html_prefix()
        );
        $_SESSION["language"] = "de";
        $this->assertEquals(
                "<html prefix=\"og: http://ogp.me/ns#\" lang=\"de\">",
                get_og_html_prefix()
        );
        unset($_SESSION["language"]);
    }

    public function testGetHtml5Doctype() {
        $this->assertEquals("<!doctype html>", get_html5_doctype());
    }

    public function testPoweredByUliCMS() {
        ob_start();
        poweredByUliCMS();
        $this->assertStringContainsString("This page is powered by",
                ob_get_clean());
    }

    public function testYear() {
        ob_start();
        year();
        $output = ob_get_clean();
        $this->assertIsNumeric($output);
        $this->assertEquals(4, strlen($output));
    }

    public function testIs503ReturnsTrue() {
        Settings::set("maintenance_mode", "on");
        $this->assertTrue(is_503());
    }

    public function testIs503ReturnsFalse() {
        Settings::set("maintenance_mode", "off");
        $this->assertFalse(is_503());
    }

    public function testGetBaseMetas() {
        $baseMetas = get_base_metas();

        $this->assertTrue(str_contains('<meta http-equiv="content-type" content="text/html; charset=utf-8"/>', $baseMetas));
        $this->assertTrue(str_contains('<meta charset="utf-8"/>', $baseMetas));
    }

    public function testBaseMetas() {
        ob_start();
        base_metas();
        $baseMetas = ob_get_clean();

        $this->assertTrue(str_contains('<meta http-equiv="content-type" content="text/html; charset=utf-8"/>', $baseMetas));
        $this->assertTrue(str_contains('<meta charset="utf-8"/>', $baseMetas));
    }

    public function testHead() {
        ob_start();
        head();
        $baseMetas = ob_get_clean();

        $this->assertTrue(str_contains('<meta http-equiv="content-type" content="text/html; charset=utf-8"/>', $baseMetas));
        $this->assertTrue(str_contains('<meta charset="utf-8"/>', $baseMetas));
    }

    public function testGetHead() {
        $baseMetas = get_head();
        $this->assertTrue(str_contains('<meta http-equiv="content-type" content="text/html; charset=utf-8"/>', $baseMetas));
        $this->assertTrue(str_contains('<meta charset="utf-8"/>', $baseMetas));
    }

    public function testIs500ReturnsFalse() {
        $this->assertFalse(is_500());
    }

    public function testGetBodyClassesDesktop() {
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1;" .
                " Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)" .
                " Chrome/63.0.3239.132 Safari/537.36";

        $cssClasses = get_body_classes();
        $this->assertStringContainsString("desktop",
                $cssClasses);
        $this->assertStringNotContainsString("mobile",
                $cssClasses);

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testBodyClassesDesktop() {
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1;" .
                " Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)" .
                " Chrome/63.0.3239.132 Safari/537.36";

        ob_start();
        body_classes();

        $cssClasses = ob_get_clean();
        $this->assertStringContainsString("desktop",
                $cssClasses);
        $this->assertStringNotContainsString("mobile",
                $cssClasses);

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testCMSReleaseYear() {
        ob_start();
        cms_release_year();
        $year = ob_get_clean();
        $this->assertIsNumeric($year);
        $this->assertGreaterThanOrEqual(2019, $year);
        // UliCMS explodes after the year 2037 caused by
        // the Year 2038 problem
        $this->assertLessThan(2038, $year);
    }

    public function testGetTextPositionWithNonExistingPageReturnsBefore() {
        $_GET["slug"] = "gibts-echt-nicht";
        $this->assertEquals("before", get_text_position());
    }

}
