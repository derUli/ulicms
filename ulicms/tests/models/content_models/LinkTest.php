<?php

class LinkTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::deleteFrom("content", "slug like 'unit_test_%'");
    }

    public function testCreateUpdateAndDeleteLink() {

        $link = new Link();
        $link->title = "Unit Test Link";
        $link->slug = "unit_test_" . uniqid();
        $link->menu = "none";
        $link->language = "de";
        $link->author_id = 1;
        $link->group_id = 1;
        $link->link_url = "https://www.google.de";
        $link->save();

        $id = $link->getID();

        $loadedLink = new Link($id);

        $this->assertIsNumeric($loadedLink->getID());
        $this->assertEquals("Unit Test Link", $loadedLink->title);
        $this->assertStringStartsWith("unit_test_", $loadedLink->slug);
        $this->assertEquals("none", $loadedLink->menu);
        $this->assertEquals("de", $loadedLink->language);
        $this->assertEquals(
                "https://www.google.de",
                $loadedLink->link_url
        );

        $this->assertEquals("link", $loadedLink->type);

        $loadedLink->title = "Unit Test Updated Link";
        $loadedLink->link_url = "https://www.ulicms.de";
        $loadedLink->save();

        $loadedLink = new Link($id);

        $this->assertEquals("Unit Test Updated Link", $loadedLink->title);
        $this->assertEquals("https://www.ulicms.de", $loadedLink->link_url);
    }

    public function testUpdateCreatesDataset() {
        $link = new Link();
        $link->title = "Unit Test Link";
        $link->slug = "unit_test_" . uniqid();
        $link->menu = "none";
        $link->language = "de";
        $link->author_id = 1;
        $link->group_id = 1;
        $link->link_url = "https://www.google.de";

        $this->assertNull($link->getID());
        $this->assertFalse($link->isPersistent());

        $link->update();

        $this->assertTrue($link->isPersistent());
        $this->assertIsNumeric($link->getID());
    }

}
