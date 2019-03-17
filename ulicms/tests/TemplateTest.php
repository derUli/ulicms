<?php

use UliCMS\Exceptions\FileNotFoundException;

class TemplateTest extends \PHPUnit\Framework\TestCase {

    private $savedSettings = array();

    public function setUp() {
        Flags::setNoCache(true);
        $this->cleanUp();

        $settings = array(
            "motto",
            "motto_de",
            "motto_en",
            "motto_fr",
            "homepage_owner"
        );
        foreach ($settings as $setting) {
            $this->savedSettings[$setting] = Settings::get($setting);
        }
        $this->setMotto();

        require_once getLanguageFilePath("en");
    }

    public function tearDown() {
        Flags::setNoCache(false);
        $this->cleanUp();

        foreach ($this->savedSettings as $key => $value) {
            Settings::set($key, $this->savedSettings[$key]);
        }
    }

    private function cleanUp() {
        unset($_SESSION["language"]);
        unset($_GET["seite"]);
        Settings::delete("video_width_100_percent");
        Settings::delete("hide_meta_generator");
        Settings::delete("disable_no_format_detection");
    }

    public function testRenderPartialSuccess() {
        $this->assertEquals("Hello World!", Template::renderPartial("hello", "impro17"));
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
        $this->assertEquals("<!doctype html>\r\n", Template::getHtml5Doctype());
        $this->assertEquals("<!doctype html>\r\n", get_html5_doctype());
    }

    public function testGetYear() {
        $this->assertEquals(date("Y"), Template::getYear());
        $this->assertEquals(date("Y"), Template::getYear("Y"));
        $this->assertEquals(date("y"), Template::getYear("y"));
    }

    public function testGetOgHTMLPrefix() {
        $_SESSION["language"] = "en";
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"en\">\r\n", Template::getOgHTMLPrefix());
        $_SESSION["language"] = "de";
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"de\">\r\n", Template::getOgHTMLPrefix());
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

        $expected = "<style type=\"text/css\">
  video {
  width: 100% !important;
  height: auto !important;
  }
           </style>
        ";
        $this->assertTrue(str_contains($expected, $baseMetas));

        Settings::delete("video_width_100_percent");
        $baseMetas = Template::getBaseMetas();
        $this->assertFalse(str_contains($expected, $baseMetas));
    }

    public function testGetBaseMetasHideMetaGenerator() {
        Settings::set("hide_meta_generator", "1");
        $expected = '<meta name="generator" content="UliCMS ' . cms_version() . '"/>';

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
        $expected = '<script src="' . $file . '?time=' . $time . '" type="text/javascript"></script>';
        $this->assertEquals($expected, Template::getjQueryScript());
    }

    public function testGetContent() {
        $_GET["seite"] = "lorem_ipsum";
        $_SESSION["language"] = "de";
        $_GET["REQUEST_URI"] = "/lorem_ipsum.html";

        $content = Template::getContent();

        $this->assertTrue(str_contains("Lorem ipsum dolor sit amet, consetetur sadipscing elitr", $content));
        $this->cleanUp();
    }

    public function testGetLanguageSelection() {
        $html = Template::getLanguageSelection();
        $this->assertTrue(str_contains("<ul class='language_selection'>", $html));

        // By default there should be at least 2 languages
        // german and english
        $this->assertGreaterThanOrEqual(2, substr_count($html, "<li>"));
        // TODO: Check if there are links in the returned html
    }

    public function testGetPoweredBy() {
        $this->assertTrue(str_contains("This page is powered by", Template::getPoweredByUliCMS()));
    }

    public function testGetHomepageOwner() {
        Settings::set("homepage_owner", "John Doe");
        $this->assertEquals("John Doe", Template::getHomepageOwner());
    }

}
