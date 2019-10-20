<?php

class ImagePageTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::deleteFrom("content", "slug like 'unit_test_%'");
    }

    public function testCreateUpdateAndDeleteLink() {

        $imagePage = new Image_Page();
        $imagePage->title = "Unit Test Link";
        $imagePage->slug = "unit_test_" . uniqid();
        $imagePage->menu = "none";
        $imagePage->language = "de";
        $imagePage->author_id = 1;
        $imagePage->group_id = 1;
        $imagePage->image_url = "foo.jpg";
        $imagePage->save();

        $id = $imagePage->getID();

        $loadedImagePage = new Image_Page($id);

        $this->assertIsNumeric($loadedImagePage->getID());
        $this->assertEquals("Unit Test Link", $loadedImagePage->title);
        $this->assertStringStartsWith("unit_test_", $loadedImagePage->slug);
        $this->assertEquals("none", $loadedImagePage->menu);
        $this->assertEquals("de", $loadedImagePage->language);
        $this->assertEquals(
                "foo.jpg",
                $loadedImagePage->image_url
        );

        $this->assertEquals("image", $loadedImagePage->type);

        $loadedImagePage->title = "Unit Test Updated Link";
        $loadedImagePage->image_url = "cats.png";
        $loadedImagePage->save();

        $loadedImagePage = new Image_Page($id);

        $this->assertEquals("Unit Test Updated Link", $loadedImagePage->title);
        $this->assertEquals("cats.png", $loadedImagePage->image_url);
    }

    public function testUpdateCreatesDataset() {
        $imagePage = new Image_Page();
        $imagePage->title = "Unit Test Link";
        $imagePage->slug = "unit_test_" . uniqid();
        $imagePage->menu = "none";
        $imagePage->language = "de";
        $imagePage->author_id = 1;
        $imagePage->group_id = 1;
        $imagePage->image_url = "foo.jpg";

        $this->assertNull($imagePage->getID());
        $this->assertFalse($imagePage->isPersistent());

        $imagePage->update();

        $this->assertTrue($imagePage->isPersistent());
        $this->assertIsNumeric($imagePage->getID());
    }

}
