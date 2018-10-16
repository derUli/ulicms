<?php
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Exceptions\NotImplementedException;

class TemplateTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        $this->cleanUp();
    }

    public function tearDown()
    {
        $this->cleanUp();
    }

    private function cleanUp()
    {
        unset($_SESSION["language"]);
        Settings::delete("video_width_100_percent");
        Settings::delete("hide_meta_generator");
        Settings::delete("disable_no_format_detection");
    }

    public function testRenderPartialSuccess()
    {
        $this->assertEquals("Hello World!", Template::renderPartial("hello", "impro17"));
    }

    public function testRenderPartialNotFound()
    {
        try {
            $nothing = Template::renderPartial("nothing", "impro17");
            $this->fail("FileNotFoundException not thrown");
        } catch (FileNotFoundException $e) {
            $this->assertNotNull("Partial not found test successfull");
        }
    }

    public function testGetHtml5Doctype()
    {
        $this->assertEquals("<!doctype html>\r\n", Template::getHtml5Doctype());
        $this->assertEquals("<!doctype html>\r\n", get_html5_doctype());
    }

    public function testGetYear()
    {
        $this->assertEquals(date("Y"), Template::getYear());
        $this->assertEquals(date("Y"), Template::getYear("Y"));
        $this->assertEquals(date("y"), Template::getYear("y"));
    }

    public function testGetOgHTMLPrefix()
    {
        $_SESSION["language"] = "en";
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"en\">\r\n", Template::getOgHTMLPrefix());
        $_SESSION["language"] = "de";
        $this->assertEquals("<html prefix=\"og: http://ogp.me/ns#\" lang=\"de\">\r\n", Template::getOgHTMLPrefix());
        unset($_SESSION["language"]);
    }

    public function testGetBaseMetas()
    {
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains('<meta http-equiv="content-type" content="text/html; charset=utf-8"/>', $baseMetas));
        $this->assertTrue(str_contains('<meta charset="utf-8"/>', $baseMetas));
    }

    public function testGetBaseMetasVideoWidth100Percent()
    {
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

    public function testGetBaseMetasHideMetaGenerator()
    {
        Settings::set("hide_meta_generator", "1");
        $expected = '<meta name="generator" content="UliCMS ' . cms_version() . '"/>';
        
        $baseMetas = Template::getBaseMetas();
        $this->assertFalse(str_contains($expected, $baseMetas));
        
        Settings::delete("hide_meta_generator");
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains($expected, $baseMetas));
    }

    public function testGetBaseMetasDisableNoFormatDetection()
    {
        Settings::set("disable_no_format_detection", "1");
        $expected = '<meta name="format-detection" content="telephone=no"/>';
        
        $baseMetas = Template::getBaseMetas();
        $this->assertFalse(str_contains($expected, $baseMetas));
        
        Settings::delete("disable_no_format_detection");
        $baseMetas = Template::getBaseMetas();
        $this->assertTrue(str_contains($expected, $baseMetas));
    }

    public function testGetMottoWithoutLanguage()
    {
        throw new NotImplementedException();
    }

    public function testGetMottoWithExistingLanguage()
    {
        throw new NotImplementedException();
    }

    public function testGetMottoWithNotExistingLanguage()
    {
        throw new NotImplementedException();
    }
    // TODO: Test für die optionalen Blöcke im <head>
}