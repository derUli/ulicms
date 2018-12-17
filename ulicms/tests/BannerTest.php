<?php

class BannerTest extends \PHPUnit\Framework\TestCase
{

    const HTML_TEXT1 = "My first Banner HTML";

    const HTML_TEXT2 = "My second Banner HTML";

    const NAME_TEXT1 = "My first Gif Banner";

    const NAME_TEXT2 = "My second Gif Banner";

    const IMAGE_URL_TEXT1 = "http://firma.de/bild.gif";

    const IMAGE_URL_TEXT2 = "http://firma.de/bild2.gif";

    const LINK_URL_TEXT1 = "http://www.google.de";

    const LINK_URL_TEXT2 = "http://www.yahoo.com";

    public function setUp()
    {
        Database::pQuery("DELETE FROM `{prefix}banner` where html in (? , ?)", array(
            self::HTML_TEXT1,
            self::HTML_TEXT1
        ), true);
    }

    public function tearDown()
    {
        $this->setUp();
    }

    public function testHTMLBannerWithoutLanguage()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->html = self::HTML_TEXT1;
        $banner->save();
        $this->assertNotNull($banner->id);
        $id = intval($banner->id);
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT1, $banner->html);
        $this->assertNull($banner->language);
        $banner->html = self::HTML_TEXT2;
        $banner->save();
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("html", $banner->getType());
        $this->assertTrue($banner->enabled);
        $this->assertEquals(self::HTML_TEXT2, $banner->html);
        $this->assertNull($banner->language);
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->id);
    }

    public function testHTMLBannerDisabledWithoutLanguage()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->html = self::HTML_TEXT1;
        $banner->enabled = false;
        $banner->save();
        $this->assertNotNull($banner->id);
        $id = intval($banner->id);
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT1, $banner->html);
        $this->assertNull($banner->language);
        $banner->html = self::HTML_TEXT2;
        $banner->save();
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("html", $banner->getType());
        $this->assertFalse($banner->enabled);
        $this->assertEquals(self::HTML_TEXT2, $banner->html);
        $this->assertNull($banner->language);
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->id);
    }

    public function testHTMLBannerWithLanguage()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->html = self::HTML_TEXT1;
        $banner->language = "de";
        $banner->save();
        $this->assertNotNull($banner->id);
        $id = intval($banner->id);
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT1, $banner->html);
        $this->assertEquals("de", $banner->language);
        $this->assertNull($banner->getDateFrom());
        $this->assertNull($banner->getDateTo());
        $banner->html = self::HTML_TEXT2;
        $banner->language = "en";
        $banner->save();
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT2, $banner->html);
        
        $this->assertEquals("en", $banner->language);
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->id);
    }

    public function testGifBannerWithoutLanguage()
    {
        $banner = new Banner();
        $banner->setType("gif");
        $banner->name = self::NAME_TEXT1;
        $banner->image_url = self::IMAGE_URL_TEXT1;
        $banner->link_url = self::LINK_URL_TEXT1;
        $banner->save();
        $this->assertNotNull($banner->id);
        $id = intval($banner->id);
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT1, $banner->name);
        $this->assertEquals(self::IMAGE_URL_TEXT1, $banner->image_url);
        $this->assertEquals(self::LINK_URL_TEXT1, $banner->link_url);
        $this->assertNull($banner->language);
        $banner->name = self::NAME_TEXT2;
        $banner->image_url = self::IMAGE_URL_TEXT2;
        $banner->link_url = self::LINK_URL_TEXT2;
        $banner->save();
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT2, $banner->name);
        $this->assertEquals(self::IMAGE_URL_TEXT2, $banner->image_url);
        $this->assertEquals(self::LINK_URL_TEXT2, $banner->link_url);
        $this->assertNull($banner->language);
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->id);
    }

    public function testGifBannerWithLanguage()
    {
        $banner = new Banner();
        $banner->setType("gif");
        $banner->language = "de";
        $banner->name = self::NAME_TEXT1;
        $banner->image_url = self::IMAGE_URL_TEXT1;
        $banner->link_url = self::LINK_URL_TEXT1;
        $banner->save();
        $this->assertNotNull($banner->id);
        $id = intval($banner->id);
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT1, $banner->name);
        $this->assertEquals(self::IMAGE_URL_TEXT1, $banner->image_url);
        $this->assertEquals(self::LINK_URL_TEXT1, $banner->link_url);
        $this->assertEquals("de", $banner->language);
        $banner->language = "en";
        $banner->name = self::NAME_TEXT2;
        $banner->image_url = self::IMAGE_URL_TEXT2;
        $banner->link_url = self::LINK_URL_TEXT2;
        $banner->save();
        $banner = new Banner($id);
        $this->assertNotNull($banner->id);
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT2, $banner->name);
        $this->assertEquals(self::IMAGE_URL_TEXT2, $banner->image_url);
        $this->assertEquals(self::LINK_URL_TEXT2, $banner->link_url);
        $this->assertEquals("en", $banner->language);
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->id);
    }

    public function testGifBannerWithDateAsString()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->html = self::HTML_TEXT1;
        $banner->enabled = false;
        $banner->setDateFrom("1992-07-27");
        $banner->setDateTo("2018-12-24");
        
        $banner->save();
        $id = $banner->id;
        $banner = new Banner($id);
        
        $this->assertEquals("1992-07-27", $banner->getDateFrom());
        $this->assertEquals("2018-12-24", $banner->getDateTo());
        
        $banner->setDateFrom("2007-04-01");
        $banner->setDateTo("2018-05-01");
        
        $banner->save();
        
        $banner = new Banner($id);
        
        $this->assertEquals("2007-04-01", $banner->getDateFrom());
        $this->assertEquals("2018-05-01", $banner->getDateTo());
        
        $banner->delete();
    }

    public function testGifBannerWithDateAsInteger()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->html = self::HTML_TEXT1;
        $banner->enabled = false;
        $banner->setDateFrom(1525348349);
        $banner->setDateTo(1546084391);
        
        $banner->save();
        $id = $banner->id;
        $banner = new Banner($id);
        
        $this->assertEquals("2018-05-03", $banner->getDateFrom());
        $this->assertEquals("2018-12-29", $banner->getDateTo());
        
        $banner->setDateFrom(1328183616);
        $banner->setDateTo(1460807642);
        
        $banner->save();
        
        $banner = new Banner($id);
        
        $this->assertEquals("2012-02-02", $banner->getDateFrom());
        $this->assertEquals("2016-04-16", $banner->getDateTo());
        
        $banner->delete();
    }
}