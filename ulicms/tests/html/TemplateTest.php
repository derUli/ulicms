<?php

use UliCMS\Exceptions\NotImplementedException;
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Utils\File;

class TemplateTest extends \PHPUnit\Framework\TestCase {

    private $savedSettings = [];

    public function setUp() {
        Flags::setNoCache(true);
        $this->cleanUp();

        $settings = array(
            "motto",
            "motto_de",
            "motto_en",
            "motto_fr",
            "homepage_owner",
            "footer_text",
            "domain_to_language"
        );
        foreach ($settings as $setting) {
            $this->savedSettings[$setting] = Settings::get($setting);
        }
        $this->setMotto();

        $_SERVER["REQUEST_URI"] = "/other-url.html?param=value";

        require_once getLanguageFilePath("en");
    }

    public function tearDown() {
        Flags::setNoCache(false);
        $this->cleanUp();

        foreach ($this->savedSettings as $key => $value) {
            Settings::set($key, $value);
        }

        Database::query("delete from {prefix}content where "
                . "title like 'Test Page %' or slug like 'testpage%' or slug ='testgetbodyclasses'",
                true);

        unset($_SERVER["REQUEST_URI"]);
        unset($_SESSION["language"]);
    }

    private function cleanUp() {
        unset($_SESSION["language"]);
        unset($_GET["seite"]);
        Settings::delete("video_width_100_percent");
        Settings::delete("hide_meta_generator");
        Settings::delete("disable_no_format_detection");
        unset($_SERVER["HTTP_USER_AGENT"]);
        unset($_GET["seite"]);
        unset($_SESSION["language"]);

        Vars::delete("id");
    }

    public function testRenderPartialSuccess() {
        $this->assertEquals("Hello World!", Template::renderPartial("hello"));
    }

    public function testRenderPartialSuccessWithTheme() {
        $this->assertEquals("Hello World!", Template::renderPartial("hello",
                        "impro17"));
    }

    public function testRenderPartialNotFound() {
        try {
            $nothing = Template::renderPartial("nothing", "impro17");
            $this->fail("FileNotFoundException not thrown");
        } catch (FileNotFoundException $e) {
            $this->assertNotNull("Partial not found test successfull");
        }
    }

    public function testGetHtml5Doctype() {
        $this->assertEquals("<!doctype html>", Template::getHtml5Doctype());
    }

    public function testHtml5Doctype() {
        ob_start();
        Template::html5Doctype();
        $this->assertEquals("<!doctype html>", ob_get_clean());
    }

    public function testGetYear() {
        $this->assertEquals(date("Y"), Template::getYear());
        $this->assertEquals(date("Y"), Template::getYear("Y"));
        $this->assertEquals(date("y"), Template::getYear("y"));
    }

    public function testYear() {
        ob_start();
        Template::year();
        $this->assertEquals(date("Y"), ob_get_clean());

        ob_start();
        Template::year("Y");
        $this->assertEquals(date("Y"), ob_get_clean());

        ob_start();
        Template::year("y");
        $this->assertEquals(date("y"), ob_get_clean());
    }

    public function testGetOgHTMLPrefix() {
        $_SESSION["language"] = "en";
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"en\">",
                Template::getOgHTMLPrefix());
        $_SESSION["language"] = "de";
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"de\">",
                Template::getOgHTMLPrefix());
        unset($_SESSION["language"]);
    }

    public function testGetBaseMetas() {
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains('<meta http-equiv="content-type" content="text/html; charset=utf-8"/>', $baseMetas));
        $this->assertTrue(str_contains('<meta charset="utf-8"/>', $baseMetas));
    }

    public function testGetBaseMetasVideoWidth100Percent() {
        Settings::set("video_width_100_percent", "1");
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains("video {", $baseMetas));
        $this->assertTrue(str_contains("width: 100% !important;", $baseMetas));
        $this->assertTrue(str_contains("height: auto !important;", $baseMetas));

        Settings::delete("video_width_100_percent");
        $baseMetas = Template::getBaseMetas();
        $this->assertFalse(str_contains("video {", $baseMetas));
    }

    public function testGetBaseMetasHideMetaGenerator() {
        Settings::set("hide_meta_generator", "1");
        $expected = '<meta name="generator" content="UliCMS '
                . cms_version() . '"/>';

        $baseMetas = Template::getBaseMetas();
        $this->assertFalse(str_contains($expected, $baseMetas));

        Settings::delete("hide_meta_generator");
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains($expected, $baseMetas));
    }

    public function testGetBaseMetasDisableNoFormatDetection() {
        Settings::set("disable_no_format_detection", "1");
        $expected = '<meta name="format-detection" content="telephone=no"/>';

        $baseMetas = Template::getBaseMetas();
        $this->assertFalse(str_contains($expected, $baseMetas));

        Settings::delete("disable_no_format_detection");
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains($expected, $baseMetas));
    }

    private function setMotto() {
        Settings::set("motto", "Motto General");
        Settings::set("motto_de", "Motto Deutsch");
        Settings::set("motto_en", "Motto English");
        Settings::delete("motto_fr");
    }

    public function testGetMottoWithoutLanguage() {
        $_SESSION["language"] = "de";
        $this->assertEquals("Motto Deutsch", Template::getMotto());

        $_SESSION["language"] = "en";
        $this->assertEquals("Motto English", Template::getMotto());
        $this->cleanUp();
    }

    public function testGetMottoWithExistingLanguage() {
        $_SESSION["language"] = "fr";
        $this->assertEquals("Motto General", Template::getMotto());
        $this->cleanUp();
    }

    public function testGetMottoWithNotExistingLanguage() {
        $this->assertEquals("Motto General", Template::getMotto());
    }

    public function testGetjQueryScript() {
        $file = "node_modules/jquery/dist/jquery.min.js";
        $time = File::getLastChanged($file);
        $expected = '<script src="' . $file . '?time=' . $time .
                '" type="text/javascript"></script>';
        $this->assertContains($expected, Template::getjQueryScript());
    }

    public function testGetContent() {
        $_GET["seite"] = "lorem_ipsum";
        $_SESSION["language"] = "de";
        $_GET["REQUEST_URI"] = "/lorem_ipsum.html";

        $content = Template::getContent();

        $this->assertTrue(str_contains("Lorem ipsum dolor sit amet, " .
                        "consetetur sadipscing elitr", $content));
        $this->cleanUp();
    }

    public function testGetLanguageSelection() {
        $html = Template::getLanguageSelection();
        $this->assertTrue(str_contains("<ul class='language_selection'>",
                        $html));

        // By default there should be at least 2 languages
        // german and english
        $this->assertGreaterThanOrEqual(2, substr_count($html, "<li>"));
        // TODO: Check if there are links in the returned html
    }

    public function testGetLanguageSelectionWithDomain2LanguageMapping() {

        $mappingLines = [
            'example.de=>de',
            'example.co.uk=>en'
        ];

        Settings::set("domain_to_language",
                implode("\n", $mappingLines));
        $html = Template::getLanguageSelection();
        $this->assertTrue(str_contains("<ul class='language_selection'>",
                        $html));

        // By default there should be at least 2 languages
        // german and english
        $this->assertGreaterThanOrEqual(2, substr_count($html, "<li>"));

        $this->assertStringContainsString("://example.de", $html);
        $this->assertStringContainsString("://example.co.uk", $html);
    }

    public function testGetPoweredByUliCMS() {
        $this->assertStringContainsString("This page is powered by",
                Template::getPoweredByUliCMS());
    }

    public function testPoweredByUliCMS() {
        ob_start();
        Template::poweredByUliCMS();
        $this->assertStringContainsString("This page is powered by",
                ob_get_clean());
    }

    public function testGetHomepageOwner() {
        Settings::set("homepage_owner", "John Doe");
        $this->assertEquals("John Doe", Template::getHomepageOwner());
    }

    public function testHomepageOwner() {
        Settings::set("homepage_owner", "John Doe");

        ob_start();
        Template::homepageOwner();

        $this->assertEquals("John Doe", ob_get_clean());
    }

    public function testGetFooterText() {
        Settings::set("footer_text", "&copy; (C) [year] by John Doe");

        $year = date("Y");

        $this->assertEquals("&copy; (C) {$year} by John Doe",
                Template::getFooterText());
    }

    public function testFooterText() {
        Settings::set("footer_text", "&copy; (C) [year] by John Doe");

        $year = date("Y");

        ob_start();
        Template::footerText();
        $this->assertEquals(
                "&copy; (C) {$year} by John Doe",
                ob_get_clean()
        );
    }

    public function testGetContentWithPlaceholder() {
        $manager = new UserManager();
        $users = $manager->getAllUsers();
        $user = $users[0];
        $user_id = $user->getId();

        $groups = Group::getAll();
        $group = $groups[0];
        $group_id = $group->getId();

        $page = new Page();
        $page->title = "Test Page " . time();
        $page->slug = "test-page-" . time();
        $page->language = "de";
        $page->menu = "not_in_menu";
        $page->content = "<p>Wir schreiben das Jahr [year] des fliegenden " .
                "Spaghettimonsters</p>";
        $page->author_id = $user_id;
        $page->group_id = $group_id;
        $page->save();

        $_SESSION["language"] = $page->language;
        $_GET["seite"] = $page->slug;

        $_GET["REQUEST_URI"] = "/{$page->slug}.html";
        $this->assertEquals("<p>Wir schreiben das Jahr " . date("Y") .
                " des fliegenden Spaghettimonsters</p>",
                Template::getContent());
    }

    public function testGetBodyClassesHome() {
        $_SESSION["language"] = "de";
        $_GET["seite"] = get_frontpage();
        $this->assertRegExp('/page-id-\d+ home page(.+)/',
                Template::getBodyClasses());

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testBodyClassesHome() {
        $_SESSION["language"] = "de";
        $_GET["seite"] = get_frontpage();

        ob_start();
        Template::bodyClasses();

        $this->assertRegExp(
                '/page-id-\d+ home page(.+)/',
                ob_get_clean()
        );

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testGetBodyClassesError403Active() {
        $page = new Page();
        $page->title = 'testgetbodyclasses';
        $page->slug = 'testpage-' . uniqid();
        $page->language = 'de';
        $page->content = "Hello World";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->access = 'all';
        $page->active = 0;
        $page->save();

        $_SESSION["language"] = $page->language;
        $_GET["seite"] = $page->slug;

        $this->assertRegExp('/page-id-\d+ error403 errorPage(.+)/',
                Template::getBodyClasses());

        $page->delete();

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testGetBodyClassesError403CauseAccess() {
        $page = new Page();
        $page->title = 'testgetbodyclasses';
        $page->slug = 'testpage-' . uniqid();
        $page->language = 'de';
        $page->content = "Hello World";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->access = strval(PHP_INT_MAX);
        $page->active = 1;
        $page->save();

        $_SESSION["language"] = $page->language;
        $_GET["seite"] = $page->slug;

        $this->assertRegExp('/page-id-\d+ error403 errorPage(.+)/',
                Template::getBodyClasses());

        $page->delete();

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testGetBodyClassesError404() {
        $_SESSION["language"] = "de";
        $_GET["seite"] = "gibts-nicht";
        $this->assertRegExp('/error404 errorPage(.+)/',
                Template::getBodyClasses());

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testGetBodyClassesMobile() {
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0" .
                " like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) " .
                "Version/5.1 Mobile/9A334 Safari/7534.48.3";
        $this->assertStringContainsString("mobile",
                Template::getBodyClasses());
        $this->assertStringNotContainsString("desktop",
                Template::getBodyClasses());

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testGetBodyClassesDesktop() {
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1;" .
                " Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)" .
                " Chrome/63.0.3239.132 Safari/537.36";
        $this->assertStringContainsString("desktop",
                Template::getBodyClasses());
        $this->assertStringNotContainsString("mobile",
                Template::getBodyClasses());

        Vars::delete("id");
        Vars::delete("active");
    }

    public function testGetBodyClassesContainsModule() {
        $_SESSION["language"] = "de";
        $_GET["seite"] = ModuleHelper::getFirstPageWithModule()->slug;
        $this->assertRegExp('/page-id-\d+ (.+)containsModule/',
                Template::getBodyClasses());
        Vars::delete("id");
        Vars::delete("active");
    }

    public function testGetDocType() {
        $this->assertEquals("<!doctype html>", Template::getDoctype());
    }

    public function testDocType() {
        ob_start();
        Template::doctype();
        $this->assertEquals("<!doctype html>", ob_get_clean());
    }

    public function testOgHTMLPrefix() {
        $_SESSION["language"] = "en";

        ob_start();
        Template::OgHTMLPrefix();
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"en\">", ob_get_clean());

        $_SESSION["language"] = "de";
        ob_start();
        Template::OgHTMLPrefix();
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"de\">", ob_get_clean());
        unset($_SESSION["language"]);
    }

}
