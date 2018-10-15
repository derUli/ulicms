<?php
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Exceptions\NotImplementedException;

class TemplateTest extends \PHPUnit\Framework\TestCase
{

    public function tearDown()
    {
        unset($_SESSION["language"]);
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
    public function testGetBaseMetas(){
        throw new NotImplementedException();
    }
}