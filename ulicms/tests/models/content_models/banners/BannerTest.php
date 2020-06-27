<?php

use UliCMS\Models\Content\Advertisement\Banner;
use UliCMS\Exceptions\DatasetNotFoundException;

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

    protected function tearDown(): void
    {
        Database::pQuery("DELETE FROM `{prefix}banner` where html in (? , ?)", array(
            self::HTML_TEXT1,
            self::HTML_TEXT2
                ), true);
    }

    public function testHTMLBannerWithoutLanguage()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->setHtml(self::HTML_TEXT1);
        $banner->save();

        $this->assertNotNull($banner->getId());
        $id = intval($banner->getId());

        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT1, $banner->getHtml());
        $this->assertNull($banner->getLanguage());
        $banner->setHtml(self::HTML_TEXT2);
        $banner->save();

        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("html", $banner->getType());
        $this->assertTrue($banner->getEnabled());
        $this->assertEquals(self::HTML_TEXT2, $banner->getHtml());
        $this->assertNull($banner->getLanguage());
        $banner->delete();

        $banner = new Banner();
        $this->assertNull($banner->getId());
    }

    public function testHTMLBannerDisabledWithoutLanguage()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->setHtml(self::HTML_TEXT1);
        $banner->setEnabled(false);
        $banner->save();
        $this->assertNotNull($banner->getId());
        $id = intval($banner->getId());
        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT1, $banner->getHtml());
        $this->assertNull($banner->getLanguage());
        $banner->setHtml(self::HTML_TEXT2);
        $banner->save();

        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("html", $banner->getType());
        $this->assertFalse($banner->getEnabled());
        $this->assertEquals(self::HTML_TEXT2, $banner->getHtml());
        $this->assertNull($banner->getLanguage());
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->getId());
    }

    public function testHTMLBannerWithLanguage()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->setHtml(self::HTML_TEXT1);
        $banner->setLanguage("de");
        $banner->save();

        $this->assertNotNull($banner->getId());
        $id = intval($banner->getId());

        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT1, $banner->getHtml());
        $this->assertEquals("de", $banner->getLanguage());
        $this->assertNull($banner->getDateFrom());
        $this->assertNull($banner->getDateTo());
        $banner->setHtml(self::HTML_TEXT2);
        $banner->setLanguage("en");
        $banner->save();

        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("html", $banner->getType());
        $this->assertEquals(self::HTML_TEXT2, $banner->getHtml());

        $this->assertEquals("en", $banner->getLanguage());
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->getId());
    }

    public function testGifBannerWithoutLanguage()
    {
        $banner = new Banner();
        $banner->setType("gif");
        $banner->setName(self::NAME_TEXT1);
        $banner->setImageUrl(self::IMAGE_URL_TEXT1);
        $banner->setLinkUrl(self::LINK_URL_TEXT1);
        $banner->save();
        $this->assertNotNull($banner->getId());
        $id = intval($banner->getId());
        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT1, $banner->getName());
        $this->assertEquals(self::IMAGE_URL_TEXT1, $banner->getImageUrl());
        $this->assertEquals(self::LINK_URL_TEXT1, $banner->getLinkUrl());
        $this->assertNull($banner->getLanguage());
        $banner->setName(self::NAME_TEXT2);
        $banner->setImageUrl(self::IMAGE_URL_TEXT2);
        $banner->setLinkUrl(self::LINK_URL_TEXT2);
        $banner->save();
        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT2, $banner->getName());
        $this->assertEquals(self::IMAGE_URL_TEXT2, $banner->getImageUrl());
        $this->assertEquals(self::LINK_URL_TEXT2, $banner->getLinkUrl());
        $this->assertNull($banner->getLanguage());
        $banner->delete();

        $banner = new Banner();
        $this->assertNull($banner->getId());
    }

    public function testGifBannerWithLanguage()
    {
        $banner = new Banner();
        $banner->setType("gif");
        $banner->setLanguage("de");
        $banner->setName(self::NAME_TEXT1);
        $banner->setImageUrl(self::IMAGE_URL_TEXT1);
        $banner->setLinkUrl(self::LINK_URL_TEXT1);
        $banner->save();
        $this->assertNotNull($banner->getId());
        $id = intval($banner->getId());

        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT1, $banner->getName());
        $this->assertEquals(self::IMAGE_URL_TEXT1, $banner->getImageUrl());
        $this->assertEquals(self::LINK_URL_TEXT1, $banner->getLinkUrl());
        $this->assertEquals("de", $banner->getLanguage());
        $banner->setLanguage("en");
        $banner->setName(self::NAME_TEXT2);
        $banner->setImageUrl(self::IMAGE_URL_TEXT2);
        $banner->setLinkUrl(self::LINK_URL_TEXT2);
        $banner->save();
        $banner = new Banner($id);
        $this->assertNotNull($banner->getId());
        $this->assertEquals("gif", $banner->getType());
        $this->assertEquals(self::NAME_TEXT2, $banner->getName());
        $this->assertEquals(self::IMAGE_URL_TEXT2, $banner->getImageUrl());
        $this->assertEquals(self::LINK_URL_TEXT2, $banner->getLinkUrl());
        $this->assertEquals("en", $banner->getLanguage());
        $banner->delete();
        $banner = new Banner();
        $this->assertNull($banner->getId());
    }

    public function testGifBannerWithDateAsString()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->setHtml(self::HTML_TEXT1);
        $banner->setEnabled(false);
        $banner->setDateFrom("1992-07-27");
        $banner->setDateTo("2018-12-24");

        $banner->save();
        $id = $banner->getId();
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
        $banner->setHtml(self::HTML_TEXT1);
        $banner->setEnabled(false);
        $banner->setDateFrom(1525348349);
        $banner->setDateTo(1546084391);

        $banner->save();
        $id = $banner->getId();
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

    public function testRenderHtmlBanner()
    {
        $banner = new Banner();
        $banner->setType("html");
        $banner->setHtml(self::HTML_TEXT1);
        $this->assertEquals(self::HTML_TEXT1, $banner->render());
    }

    public function testRenderGifBanner()
    {
        $banner = new Banner();
        $banner->setType("gif");
        $banner->setName(self::NAME_TEXT1);
        $banner->setImageUrl(self::IMAGE_URL_TEXT1);
        $banner->setLinkUrl(self::LINK_URL_TEXT1);
        $this->assertEquals(
            '<a href="http://www.google.de" target="_blank">'
                . '<img src="http://firma.de/bild.gif" '
                . 'title="My first Gif Banner" alt="My first Gif Banner" '
                . 'border="0"></a>',
            $banner->render()
        );
    }

    public function testSetDateFromThrowsException()
    {
        $banner = new Banner();

        $this->expectException("InvalidArgumentException");
        $banner->setDateFrom(new Page());
    }

    public function testSetDateToThrowsException()
    {
        $banner = new Banner();

        $this->expectException("InvalidArgumentException");
        $banner->setDateTo(new Page());
    }

    public function testLoadByIdNotFound()
    {
        $banner = new Banner();

        $this->expectException(DatasetNotFoundException::class);

        $banner->loadByID(PHP_INT_MAX);

        $this->assertFalse($banner->isPersistent());
    }

    private function createTestBanners()
    {
        for ($i = 1; $i < 20; $i++) {
            $banner = new Banner();
            $banner->setType("html");
            $banner->setHtml(self::HTML_TEXT1);
            $banner->save();
        }
    }

    public function testLoadRandom()
    {
        $this->createTestBanners();

        $banner1 = new Banner();
        $banner1->loadRandom();

        $this->assertGreaterThanOrEqual(1, $banner1->getId());

        for ($i = 1; $i <= 3; $i++) {
            $banner2 = new Banner();
            $banner2->loadRandom();

            $this->assertGreaterThanOrEqual(1, $banner2->getId());
            if ($banner1->getId() !== $banner2->getId()) {
                $this->assertNotEquals($banner2->getId(), $banner1->getId());
                return;
            }
        }

        $this->assertNotEquals($banner2->getId(), $banner1->getId());
    }

    public function testCreateWithAllEmpty()
    {
        $banner = new Banner();
        $banner->setCategoryId(null);
        $banner->save(); // insert
        $banner->save(); // update

        $this->assertGreaterThanOrEqual(1, $banner->getId());

        $banner->delete();
    }

    public function testUpdateWithoutInsert()
    {
        $banner = new Banner();
        $banner->setType("html");

        $banner->setHtml(self::HTML_TEXT1);
        $this->assertFalse($banner->isPersistent());
        $banner->update();

        $this->assertTrue($banner->isPersistent());

        $banner->delete();
    }
}
