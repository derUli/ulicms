<?php

use UliCMS\Models\Content\Category;
use UliCMS\Models\Content\Categories;

class CategoryControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        LoggerRegistry::register(
                "audit_log",
                new Logger(Path::resolve("ULICMS_LOG/audit_log"))
        );
    }

    public function tearDown() {
        LoggerRegistry::unregister("audit_log");
        Database::deleteFrom("categories", "name like 'Unit Test%'");
    }

    public function testCreateCategory() {
        $name = "Unit Test " . time();
        $description = "Description " . time();
        $controller = new CategoryController();
        $id = $controller->_createPost($name, $description);

        $this->assertGreaterThan(1, $id);

        $banner = new Category($id);
        $this->assertEquals($name, $banner->getName());
        $this->assertEquals($description, $banner->getDescription());
    }

    public function testUpdateCategory() {
        $name = "Unit Test " . time();
        $description = "Description " . time();
        $createdId = Categories::addCategory($name, $description);

        $controller = new CategoryController();
        $updatedId = $controller->_updatePost(
                $createdId,
                "Unit Test New Name",
                "New Description"
        );

        $this->assertGreaterThan(1, $updatedId);

        $banner = new Category($createdId);
        $this->assertEquals("Unit Test New Name", $banner->getName());
        $this->assertEquals("New Description", $banner->getDescription());
    }
    
    public function testDeleteCategoryReturnsTrue() {
        $name = "Unit Test " . time();
        $description = "Description " . time();
        $createdId = Categories::addCategory($name, $description);

        $controller = new CategoryController();
        $success = $controller->_deletePost($createdId);

        $this->assertTrue($success);

        $category = new Category($createdId);
        $this->assertNull($category->getID());
    }
    // can't delete "General" category
    public function testDeleteCategoryReturnsFalse() {
        $controller = new CategoryController();
        $success = $controller->_deletePost(1);

        $this->assertFalse($success);

        $category = new Category(1);
        $this->assertEquals(1, $category->getID());
    }

}
