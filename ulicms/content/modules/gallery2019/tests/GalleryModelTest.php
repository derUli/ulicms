<?php
use Gallery2019\Gallery;

class GalleryModelTest extends \PHPUnit\Framework\TestCase
{

    public function tearDown()
    {
        Database::query("delete from `{prefix}gallery` where title like 'Test - %'", true);
    }

    public function testCreateEditAndDeleteGallery()
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
        
        $gallery->delete();
    }
}