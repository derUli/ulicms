<?php

use App\Models\Content\Advertisement\Banner;

class TemplatingTest extends \PHPUnit\Framework\TestCase
{
    private $homepageOwner;
    private $initialMobileTheme;
    private $initialDomainToLanguage;

    public const HTML_TEXT1 = "My first Banner HTML";

    protected function setUp(): void
    {
        $this->initialMobileTheme = Settings::get("mobile_theme");
        $this->homepageOwner = Settings::get("homepage_owner");
        $this->initialDomainToLanguage = Settings::get("domain_to_language");

        $_SESSION["language"] = "de";
        $_GET["slug"] = get_frontpage();
        require_once getLanguageFilePath("en");

        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = "80";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo.html";
        App\Utils\Session\sessionStart();
    }

    protected function tearDown(): void
    {
        $this->cleanUp();

        Settings::get("mobile_theme", $this->initialMobileTheme);
        Settings::set("homepage_owner", $this->homepageOwner);
        Settings::set("maintenance_mode", "off");
        Settings::set("domain_to_language", $this->initialDomainToLanguage);

        unset($_SERVER["SERVER_PROTOCOL"]);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['REQUEST_URI']);
        unset($_GET["slug"]);
        unset($_SESSION["login_id"]);
        unset($_SESSION["language"]);

        App\Utils\Session\sessionDestroy();

        Database::deleteFrom("users", "username like 'testuser_%'");
        Database::deleteFrom("content", "slug like 'unit-test%'");
        Database::pQuery("DELETE FROM `{prefix}banner` where html like ?", array(
            self::HTML_TEXT1 . "%",
                ), true);
    }

    private function cleanUp()
    {
        Vars::delete("title");
        Vars::delete("headline");
        Vars::delete("page");
        Vars::delete("type");
        Vars::delete("cache_control");

        Database::query("delete from {prefix}content where slug = 'testdisableshortcodes' or title like 'Unit Test%'", true);
    }

    public function testGetRequestedPageNameWithSlugSet()
    {
        $_GET ["slug"] = "foobar";
        $this->assertEquals("foobar", get_slug());
        $this->assertEquals("foobar", get_requested_pagename());
        $this->cleanUp();
    }

    public function testGetRequestedPageNameWithoutSlug()
    {
        $this->cleanUp();
        $this->assertEquals(get_frontpage(), get_slug());
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testGetRequestedPageNameWithNull()
    {
        $_GET ["slug"] = null;
        $this->assertEquals(get_frontpage(), get_slug());
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testGetRequestedPageNameWithEmptyString()
    {
        $_GET ["slug"] = "";
        $this->assertEquals(get_frontpage(), get_slug());
        $this->assertEquals(get_frontpage(), get_requested_pagename());
    }

    public function testIsHomeTrue()
    {
        $_GET ["slug"] = get_frontpage();
        $this->assertTrue(is_home());
        $this->cleanUp();
    }

    public function testIsHomeFalse()
    {
        $_GET ["slug"] = "nothome";
        $this->assertFalse(is_home());
        $this->cleanUp();
    }

    public function testIsFrontPageTrue()
    {
        $_GET ["slug"] = get_frontpage();
        $this->assertTrue(is_frontpage());
        $this->cleanUp();
    }

    public function testIsFrontPageFalse()
    {
        $_GET ["slug"] = "nothome";
        $this->assertFalse(is_frontpage());
        $this->cleanUp();
    }

    public function testGetType()
    {
        $content1 = new Module_Page();
        $content1->title = 'Unit Test ' . uniqid();
        $content1->slug = 'unit-test-' . uniqid();
        $content1->language = 'de';
        $content1->content = "even more text";
        $content1->comments_enabled = false;
        $content1->author_id = 1;
        $content1->group_id = 1;
        $content1->save();

        $this->assertEquals(
            "module",
            get_type(
                $content1->slug,
                $content1->language
            )
        );

        $content1->type = "video";
        $content1->save();

        // The type is cached so get_type() returns the same
        $this->assertEquals(
            "module",
            get_type(
                $content1->slug,
                $content1->language
            )
        );
        // unset the cached type
        Vars::delete("type_{$content1->slug}_{$content1->language}");

        // no it should get the actual type (video)
        $this->assertEquals(
            "video",
            get_type(
                $content1->slug,
                $content1->language
            )
        );

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
        $this->assertEquals(
            "article",
            get_type(
                $content2->slug,
                $content2->language
            )
        );
    }

    public function testSetRequestedPageName()
    {
        set_requested_pagename("my-slug", "en");
        $this->assertEquals("my-slug", get_slug());
        $this->assertEquals("en", Request::getVar("language"));
    }

    public function testSetRequestedPageNameWithoutLanguage()
    {
        set_requested_pagename("my-slug");

        $this->assertEquals("my-slug", get_slug());
        $this->assertEquals("de", $_SESSION["language"]);
    }

    public function testGetMenu()
    {
        $_SESSION["language"] = 'en';
        $html = get_menu("top", null, false);
        $this->assertStringContainsString("<ul", $html);
        $this->assertStringContainsString("<li", $html);
        $this->assertStringContainsString("menu_top", $html);
        $this->assertStringContainsString("<a href", $html);

        $pages = Contentfactory::getAllByMenuAndLanguage("top", "en");
        foreach ($pages as $page) {
            if (!$page->isFrontPage() && $page->isRegular() && !$page->getParent()) {
                $this->assertStringContainsString($page->slug, $html);
                $this->assertStringContainsString($page->title, $html);
            }
        }
        $germanPages = Contentfactory::getAllByLanguage("de");
        foreach ($germanPages as $page) {
            $this->assertStringNotContainsString($page->title . ".html", $html);
        }
    }

    public function testMenu()
    {
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
                $this->assertStringContainsString($page->slug, $html);
                $this->assertStringContainsString($page->title, $html);
            }
        }

        $this->assertStringNotContainsString("Willkommen", $html);
    }

    public function testHtml5Doctype()
    {
        ob_start();
        html5_doctype();
        $this->assertEquals("<!doctype html>", ob_get_clean());
    }

    public function testOgHTMLPrefix()
    {
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

    public function testGetOgHTMLPrefix()
    {
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

    public function testGetHtml5Doctype()
    {
        $this->assertEquals("<!doctype html>", get_html5_doctype());
    }

    public function testPoweredByUliCMS()
    {
        ob_start();
        poweredByUliCMS();
        $this->assertStringContainsString(
            "This page is powered by",
            ob_get_clean()
        );
    }

    public function testYear()
    {
        ob_start();
        year();
        $output = ob_get_clean();
        $this->assertIsNumeric($output);
        $this->assertEquals(4, strlen($output));
    }

    public function testGetBaseMetas()
    {
        $baseMetas = get_base_metas();

        $this->assertTrue(str_contains($baseMetas, '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>'));
        $this->assertTrue(str_contains($baseMetas, '<meta charset="utf-8"/>'));
    }

    public function testBaseMetas()
    {
        ob_start();
        base_metas();
        $baseMetas = ob_get_clean();

        $this->assertTrue(str_contains($baseMetas, '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>'));
        $this->assertTrue(str_contains($baseMetas, '<meta charset="utf-8"/>'));
    }

    public function testHead()
    {
        ob_start();
        head();
        $baseMetas = ob_get_clean();

        $this->assertTrue(str_contains($baseMetas, '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>'));
        $this->assertTrue(str_contains($baseMetas, '<meta charset="utf-8"/>'));
    }

    public function testGetHead()
    {
        $baseMetas = get_head();
        $this->assertTrue(str_contains($baseMetas, '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>'));
        $this->assertTrue(str_contains($baseMetas, '<meta charset="utf-8"/>'));
    }

    public function testGetBodyClassesDesktop()
    {
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1;" .
                " Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)" .
                " Chrome/63.0.3239.132 Safari/537.36";

        $cssClasses = get_body_classes();
        $this->assertStringContainsString(
            "desktop",
            $cssClasses
        );
        $this->assertStringNotContainsString(
            "mobile",
            $cssClasses
        );

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testBodyClassesDesktop()
    {
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1;" .
                " Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)" .
                " Chrome/63.0.3239.132 Safari/537.36";

        ob_start();
        body_classes();

        $cssClasses = ob_get_clean();
        $this->assertStringContainsString(
            "desktop",
            $cssClasses
        );
        $this->assertStringNotContainsString(
            "mobile",
            $cssClasses
        );

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testCMSReleaseYear()
    {
        ob_start();
        cms_release_year();
        $year = ob_get_clean();
        $this->assertIsNumeric($year);
        $this->assertGreaterThanOrEqual(2019, $year);

        // UliCMS explodes after the year 2037 caused by
        // the Year 2038 problem
        $this->assertLessThan(2038, $year);
    }

    public function testGetTextPositionWithNonExistingPageReturnsBefore()
    {
        $_GET["slug"] = "gibts-echt-nicht";
        $this->assertEquals("before", get_text_position());
    }

    public function testGetMotto()
    {
        $slogan1 = get_motto();

        $this->assertNotEmpty($slogan1);

        ob_start();

        motto();

        $slogan2 = ob_get_clean();

        $this->assertNotEmpty($slogan2);

        $this->assertEquals($slogan1, $slogan2);
    }

    public function testHomepageOwner()
    {
        Settings::set("homepage_owner", "John Doe");

        ob_start();
        homepage_owner();
        $this->assertEquals("John Doe", ob_get_clean());
    }

    private function createTestBanners()
    {
        for ($i = 1; $i < 20; $i++) {
            $banner = new Banner();
            $banner->setType("html");
            $banner->setHtml(
                self::HTML_TEXT1 . " " . uniqid()
            );
            $banner->save();
        }
    }

    public function testRandomBanner()
    {
        $this->createTestBanners();

        ob_start();
        random_banner();
        $banner1 = ob_get_clean();
        $this->assertNotEmpty($banner1);

        for ($i = 0; $i <= 3; $i++) {
            ob_start();
            random_banner();
            $banner2 = ob_get_clean();

            if ($banner2 !== $banner1) {
                $this->assertNotEmpty($banner2);
                return;
            }
        }

        $this->fail("Test failed");
    }

    public function testLanguageSelection()
    {
        ob_start();
        language_selection();
        $html = ob_get_clean();

        $this->assertTrue(
            str_contains(
                $html,
                "<ul class='language_selection'>",
            )
        );

        // By default there should be at least 2 languages
        // german and english
        $this->assertGreaterThanOrEqual(2, substr_count($html, "<li>"));
        // TODO: Check if there are links in the returned html
    }

    public function testCategory()
    {
        ob_start();
        category();
        $this->assertEquals("Allgemein", ob_get_clean());
    }

    public function testCategoryId()
    {
        ob_start();
        category_id();
        $this->assertEquals("1", ob_get_clean());
    }

    public function testHomepageTitle()
    {
        ob_start();
        homepage_title();
        $this->assertNotEmpty(ob_get_clean());
    }

    public function testHeadline()
    {
        ob_start();
        headline();
        $this->assertNotEmpty(ob_get_clean());
    }

    public function testOgTags()
    {
        ob_start();
        og_tags();
        $html = ob_get_clean();

        // TODO: check the html content
        $this->assertNotEmpty($html);
    }

    public function getTestUser()
    {
        $user = new User();
        $user->setUsername("testuser_" . uniqid());
        $user->setFirstname("Max");
        $user->setLastname("Muster");
        $user->setGroupId(1);
        $user->setPassword("password123");
        $user->setEmail("max@muster.de");
        $user->setHomepage("http://www.google.de");
        $user->setDefaultLanguage("fr");
        $user->setHTMLEditor("ckeditor");
        $user->setFailedLogins(0);

        $user->setAboutMe("hello world");
        $lastLogin = time();
        $user->setLastLogin($lastLogin);
        $user->setAdmin(true);
        $user->save();

        return $user;
    }

    public function testGetEditButtonReturnsHtml()
    {
        $user = $this->getTestUser();
        $user->registerSession(false);

        Vars::setNoCache(true);

        $html = get_edit_button();

        $this->assertStringStartsWith('<div class="ulicms-edit"><a href="admin/?action=pages_edit&amp;page', $html);
        $this->assertStringEndsWith('class="btn btn-warning btn-edit">Edit</a></div>', $html);

        Vars::setNoCache(false);
    }

    public function testGetEditButtonReturnsEmptyString()
    {
        $this->assertEmpty(get_edit_button());
    }

    public function testEditButtonOutputsHtml()
    {
        $user = $this->getTestUser();
        $user->registerSession(false);

        Vars::setNoCache(true);
        ob_start();
        edit_button();
        $html = ob_get_clean();

        $this->assertStringStartsWith('<div class="ulicms-edit"><a href="admin/?action=pages_edit&amp;page', $html);
        $this->assertStringEndsWith('class="btn btn-warning btn-edit">Edit</a></div>', $html);

        Vars::setNoCache(false);
    }

    public function testGetCacheControl()
    {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->slug = "unit-test-" . uniqid();
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;
        $article->cache_control = "force";

        $article->save();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $this->assertEquals("force", get_cache_control());
        $this->assertEquals("force", get_cache_control());
    }

    public function testGetTextPosition()
    {
        $this->assertStringContainsString("before", get_text_position());
    }

    public function testGetArticleMeta()
    {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->slug = "unit-test-" . uniqid();
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;

        $article->article_author_name = 'Lara Croft';
        $article->article_author_email = 'lara@croft.com';
        $article->article_date = mktime(4, 20, 15, 4, 1, 2019);
        $article->excerpt = "Das ist der Ausschnitt";

        $article->save();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $article_meta = get_article_meta();

        $this->assertEquals("Lara Croft", $article_meta->article_author_name);
        $this->assertEquals("lara@croft.com", $article_meta->article_author_email);
        $this->assertEquals(1554085215, $article_meta->article_date);
        $this->assertEquals("Das ist der Ausschnitt", $article_meta->excerpt);
    }

    public function testGetOgData()
    {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->slug = "unit-test-" . uniqid();
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;

        $article->article_author_name = 'Lara Croft';
        $article->article_author_email = 'lara@croft.com';
        $article->article_date = mktime(4, 20, 15, 4, 1, 2019);
        $article->excerpt = "Das ist der Ausschnitt";

        $article->og_title = 'Open Graph Titel';
        $article->og_description = "Open Graph Beschreibung";

        $article->og_image = "/content/images/grafik.jpg";
        $article->save();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $ogData = get_og_data();

        $this->assertEquals(
            "Open Graph Titel",
            $ogData["og_title"]
        );
        $this->assertEquals(
            "Open Graph Beschreibung",
            $ogData["og_description"]
        );
        $this->assertEquals(
            "/content/images/grafik.jpg",
            $ogData["og_image"]
        );
    }

    public function testGetAccess()
    {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->slug = "unit-test-" . uniqid();
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;

        $article->article_author_name = 'Lara Croft';
        $article->article_author_email = 'lara@croft.com';
        $article->article_date = mktime(4, 20, 15, 4, 1, 2019);
        $article->excerpt = "Das ist der Ausschnitt";

        $article->access = "mobile,2,5,8";
        $article->save();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $this->assertEquals(
            [
                "mobile",
                "2",
                "5",
                "8"
            ],
            get_access()
        );
    }

    public function testGetRedirection()
    {
        $link = new Link();
        $link->title = "Unit Test Article";
        $link->slug = "unit-test-" . uniqid();
        $link->menu = "none";
        $link->language = "de";
        $link->article_date = 1413821696;
        $link->author_id = 1;
        $link->group_id = 1;

        $link->link_url = "https://www.ulicms.de";

        $link->access = "mobile,2,5,8";
        $link->save();

        $_GET["slug"] = $link->slug;
        $_SESSION["language"] = "de";

        $this->assertEquals("https://www.ulicms.de", get_redirection());
    }

    public function testGetTheme()
    {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->slug = "unit-test-" . uniqid();
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;

        $article->article_author_name = 'Lara Croft';
        $article->article_author_email = 'lara@croft.com';
        $article->article_date = mktime(4, 20, 15, 4, 1, 2019);
        $article->excerpt = "Das ist der Ausschnitt";
        $article->theme = "2020";
        $article->save();

        Settings::set("mobile_theme", "impro17");

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3";

        $this->assertEquals("2020", get_theme());
    }

    public function testGetCategory()
    {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->slug = "unit-test-" . uniqid();
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;
        $article->category_id = null;

        $article->article_author_name = 'Lara Croft';
        $article->article_author_email = 'lara@croft.com';
        $article->article_date = mktime(4, 20, 15, 4, 1, 2019);
        $article->excerpt = "Das ist der Ausschnitt";
        $article->theme = "2020";
        $article->save();

        Settings::set("mobile_theme", "impro17");

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3";

        $this->assertEmpty(get_category());
    }

    public function testGetTypeNotFound()
    {
        $this->assertNull(get_type("gibts_echt_nicht", "de"));
    }

    public function testGetMetaDescriptionFromPage()
    {
        $article = $this->getArticleWithMetaData();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $this->assertEquals("Bla Bla usw.", get_meta_description());
    }

    public function testMetaDescriptionFromPage()
    {
        $article = $this->getArticleWithMetaData();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        ob_start();
        meta_description();
        $this->assertEquals("Bla Bla usw.", ob_get_clean());
    }

    private function getArticleWithMetaData(): Article
    {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->slug = "unit-test-" . uniqid();
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;
        $article->category_id = null;

        $article->meta_description = "Bla Bla usw.";
        $article->meta_keywords = "word 1, word 2, word 3";

        $article->excerpt = "Das ist der Ausschnitt";

        $all = ContentFactory::getAllByLanguage($article->language);
        $first = $all[0];

        $article->parent_id = $first->getId();
        $article->theme = "2020";
        $article->save();
        return $article;
    }

    public function testGetParentReturnsId()
    {
        $article = $this->getArticleWithMetaData();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $this->assertGreaterThanOrEqual(1, get_parent());
    }

    public function testGetParentReturnsNull()
    {
        $article = $this->getArticleWithMetaData();
        $article->parent_id = null;
        $article->save();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        $this->assertNull(get_parent());
    }

    public function testGetFrontpage()
    {
        unset($_SESSION["language"]);
        $this->assertIsString(get_frontpage());
        $this->assertNotEmpty(get_frontpage());
    }

    public function testTitleReturnsTitle()
    {
        $article = $this->getArticleWithMetaData();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        ob_start();
        title();

        $this->assertEquals("Unit Test Article", ob_get_clean());
    }

    public function testTitleReturnsAlternateTitle()
    {
        $article = $this->getArticleWithMetaData();
        $article->alternate_title = "Alternativer Titel";
        $article->save();

        $_GET["slug"] = $article->slug;
        $_SESSION["language"] = "de";

        ob_start();
        title(null, true);

        $this->assertEquals("Alternativer Titel", ob_get_clean());

        ob_start();
        title(null, true);

        $this->assertEquals("Alternativer Titel", ob_get_clean());
    }

    public function testParentItemContainsCurrentPageWithNullReturnsTrue()
    {
        set_requested_pagename("glueckskeks", "de");
        $page = ContentFactory::getBySlugAndLanguage("module", "de");

        $this->assertTrue(
            parent_item_contains_current_page(
                $page->getId()
            )
        );
    }

    public function testParentItemContainsCurrentPageWithNullReturnsFalse()
    {
        $this->assertFalse(
            parent_item_contains_current_page(null)
        );
    }
}
