<?php
use Gallery2019\Gallery;
use Gallery2019\Image;

class GalleryModelTest extends \PHPUnit\Framework\TestCase
{

    public function tearDown()
    {
        Database::query("delete from `{prefix}gallery` where title like 'Test - %'", true);
    }

    public function testCreateEditAndDeleteGallery()
    {
        $this->assertEquals(0, count(Gallery::getAll()));
        
        $manager = new UserManager();
        $users = $manager->getAllUsers();
        $firstUser = $users[0];
        
        $gallery = new Gallery();
        $this->assertNull($gallery->getID());
        $title = "Test - Created at " . time();
        $gallery->setTitle($title);
        $gallery->setCreatedBy($firstUser->getId());
        $gallery->setLastChangedBy($firstUser->getId());
        $gallery->save();
        
        $id = $gallery->getID();
        $createdTime = $gallery->getCreated();
        
        $this->assertNotNull($id);
        
        $gallery = new Gallery($id);
        $this->assertEquals($title, $gallery->getTitle());
        $this->assertGreaterThanOrEqual(time() - 500, $gallery->getCreated());
        $this->assertGreaterThanOrEqual(time() - 500, $gallery->getUpdated());
        $this->assertEquals($firstUser->getID(), $gallery->getCreatedBy());
        $this->assertEquals($firstUser->getID(), $gallery->getLastChangedBy());
        
        $newTitle = "Test - Updated at " . time();
        $gallery->setTitle($newTitle);
        
        $gallery->save();
        
        $this->assertEquals(1, count(Gallery::getAll()));
        
        $gallery = new Gallery($id);
        $this->assertEquals($newTitle, $gallery->getTitle());
        $this->assertGreaterThanOrEqual($createdTime, $gallery->getCreated());
        $this->assertGreaterThanOrEqual($createdTime - 500, $gallery->getUpdated());
        $this->assertEquals($firstUser->getID(), $gallery->getCreatedBy());
        $this->assertEquals($firstUser->getID(), $gallery->getLastChangedBy());
        
        $gallery->delete();
        
        $gallery = new Gallery($id);
        $this->assertNull($gallery->getID());
    }

    public function testCreateGalleryAddImages()
    {
        $manager = new UserManager();
        $users = $manager->getAllUsers();
        $firstUser = $users[0];
        
        $gallery = new Gallery();
        $this->assertNull($gallery->getID());
        $title = "Test - Created at " . time();
        $gallery->setTitle($title);
        $gallery->setCreatedBy($firstUser->getId());
        $gallery->setLastChangedBy($firstUser->getId());
        $gallery->save();
        
        $this->assertEquals(0, count($gallery->getImages()));
        
        $id = $gallery->getID();
        
        $gallery = new Gallery($id);
        
        $this->assertEquals(0, count($gallery->getImages()));
        
        $imageOk = new Image();
        $imageOk->setPath("/admin/gfx/logo.png");
        $imageOk->setDescription("Test Image");
        $imageOk->setOrder(10);
        $gallery->addImage($imageOk);
        
        $imageFailed = new Image();
        $imageFailed->setPath("/content/images/nothing.jpg");
        $imageFailed->setDescription("Not existing Image");
        $imageFailed->setOrder(20);
        $gallery->addImage($imageFailed);
        
        $this->assertEquals(2, count($gallery->getImages()));
        
        $gallery = new Gallery($id);
        $this->assertEquals(2, count($gallery->getImages()));
        
        $images = $gallery->getImages();
        $firstImage = $images[0];
        $lastImage = $images[1];
        
        $this->assertEquals("/admin/gfx/logo.png", $firstImage->getPath());
        $this->assertEquals("Test Image", $firstImage->getDescription());
        $this->assertEquals(10, $firstImage->getOrder());
        $this->assertEquals($imageOk->getGalleryId(), $firstImage->getGalleryId());
        $this->assertTrue($firstImage->exists());
        
        $this->assertEquals("/admin/gfx/logo.png", $firstImage->getPath());
        $this->assertEquals("Test Image", $firstImage->getDescription());
        $this->assertEquals(10, $firstImage->getOrder());
        $this->assertEquals($imageOk->getGalleryId(), $firstImage->getGalleryId());
        $this->assertTrue($firstImage->exists());
        
        $this->assertEquals("/content/images/nothing.jpg", $lastImage->getPath());
        $this->assertEquals("Not existing Image", $lastImage->getDescription());
        $this->assertEquals(20, $lastImage->getOrder());
        $this->assertEquals($imageFailed->getGalleryId(), $lastImage->getGalleryId());
        $this->assertFalse($lastImage->exists());
        
        $lastImage->delete();
        $this->assertEquals(1, count($gallery->getImages()));
        
        $firstImage->setDescription("Test New Description");
        $firstImage->setPath("/new/path.jpg");
        $firstImage->setOrder(666);
        $firstImage->save();
        
        $images = $gallery->getImages();
        $firstImage = $images[0];
        
        $this->assertEquals("/new/path.jpg", $firstImage->getPath());
        $this->assertEquals("Test New Description", $firstImage->getDescription());
        $this->assertEquals(666, $firstImage->getOrder());
        $this->assertEquals($imageOk->getGalleryId(), $firstImage->getGalleryId());
        
        $gallery->delete();
    }
}