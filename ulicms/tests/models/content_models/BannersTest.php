<?php

class BannersTest extends \PHPUnit\Framework\TestCase {

    const HTML_TEXT1 = "My first Banner HTML";
    const HTML_TEXT2 = "My second Banner HTML";

    public function tearDown() {
        Database::pQuery("DELETE FROM `{prefix}banner` where html in (? , ?)", array(
            self::HTML_TEXT1,
            self::HTML_TEXT2
                ), true);

        Database::query("DELETE FROM `{prefix}categories` where title like 'Testkategorie %'", true);
    }

    public function testGetByCategoryExpectEmptyResult() {
        $result = Banners::getByCategory(PHP_INT_MAX);
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    public function testGetByCategoryExpectArrayOfBanners() {
        $category1 = new Category();
        $category1->setName("Testkategorie " . uniqid());
        $category1->save();

        $category2 = new Category();
        $category2->setName("Testkategorie " . uniqid());
        $category2->save();

        $banner1 = new Banner();
        $banner1->setType("html");
        $banner1->setHtml(self::HTML_TEXT1);
        $banner1->setCategoryId($category1->getID());
        $banner1->save();

        $banner2 = new Banner();
        $banner2->setType("html");
        $banner2->setHtml(self::HTML_TEXT1);
        $banner2->setCategoryId($category1->getID());
        $banner2->save();

        $banner3 = new Banner();
        $banner3->setType("gif");
        $banner3->setHtml(self::HTML_TEXT1);
        $banner3->setCategoryId($category2->getID());
        $banner3->save();

        $result1 = Banners::getByCategory($category1->getID());
        $this->assertCount(2, $result1);
        foreach ($result1 as $banner) {
            $this->assertInstanceOf(Banner::class, $banner);
            $this->assertEquals($category1->getID(), $banner->getCategoryId());
        }

        $result2 = Banners::getByCategory($category2->getID());
        $this->assertCount(1, $result2);
        foreach ($result2 as $banner) {
            $this->assertInstanceOf(Banner::class, $banner);
            $this->assertEquals($category2->getID(), $banner->getCategoryId());
        }
    }

    public function testGetByTypeExpectArrayOfBanners() {
        $gifBanners = Banners::getByType("gif");

        $this->assertGreaterThanOrEqual(1, $gifBanners);
        foreach ($gifBanners as $banner) {
            $this->assertEquals("gif", $banner->getType());
        }

        $htmlBanners = Banners::getByType("html");
        $this->assertGreaterThanOrEqual(1, $htmlBanners);

        foreach ($htmlBanners as $banner) {
            $this->assertEquals("html", $banner->getType());
        }
    }

    public function testGetByLanguageExpectArrayOfBanners() {
        $category1 = new Category();
        $category1->setName("Testkategorie " . uniqid());
        $category1->save();

        $category2 = new Category();
        $category2->setName("Testkategorie " . uniqid());
        $category2->save();

        $banner1 = new Banner();
        $banner1->setType("html");
        $banner1->setHtml(self::HTML_TEXT1);
        $banner1->setCategoryId($category1->getID());
        $banner1->setLanguage("en");
        $banner1->save();

        $banner2 = new Banner();
        $banner2->setType("html");
        $banner2->setHtml(self::HTML_TEXT1);
        $banner2->setCategoryId($category1->getID());
        $banner2->setLanguage("de");
        $banner2->save();

        $germanBanners = Banners::getByLanguage("de");

        $this->assertGreaterThanOrEqual(1, count($germanBanners));
        foreach ($germanBanners as $banner) {
            $this->assertEquals("de", $banner->getLanguage());
        }

        $englishBanners = Banners::getByLanguage("en");
        $this->assertGreaterThanOrEqual(1, count($englishBanners));

        foreach ($englishBanners as $banner) {
            $this->assertEquals("en", $banner->getLanguage());
        }
    }

    public function testGetAll() {
        $banners = Banners::getAll();
        $this->assertIsArray($banners);
        $this->assertGreaterThanOrEqual(1, count($banners));
        foreach ($banners as $banner) {
            $this->assertInstanceOf(Banner::class, $banner);
            $this->assertGreaterThanOrEqual(1, $banner->getId());
        }
    }

    public function testGetRandom() {

        $_SESSION["language"] = "de";

        for ($i = 1; $i < 5; $i++) {
            $banner = new Banner();
            $banner->setType("html");
            $banner->setHtml(self::HTML_TEXT1);
            $banner->save();
        }

        $banner1 = Banners::getRandom();
        $this->assertInstanceOf(Banner::class, $banner1);


        $banner2 = Banners::getRandom();

        $i = 1;

        do {
            $banner2 = Banners::getRandom();
            $i++;
        } while ($banner1->getId() == $banner2->getId() and
        $i < 10);

        $this->assertNotEquals($banner2->getId(), $banner1->getId());
    }

}
