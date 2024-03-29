<?php

use App\Models\Content\Categories;
use App\Models\Content\Category;

class CategoryControllerTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::deleteFrom('categories', "name like 'Unit Test%'");
    }

    public function testCreateCategory(): void {
        $name = 'Unit Test ' . time();
        $description = 'Description ' . time();
        $controller = new CategoryController();
        $id = $controller->_createPost($name, $description);

        $this->assertGreaterThan(1, $id);

        $category = new Category($id);
        $this->assertEquals($name, $category->getName());
        $this->assertEquals($description, $category->getDescription());
    }

    public function testUpdateCategory(): void {
        $name = 'Unit Test ' . time();
        $description = 'Description ' . time();
        $createdId = Categories::addCategory($name, $description);

        $controller = new CategoryController();
        $updatedId = $controller->_updatePost(
            $createdId,
            'Unit Test New Name',
            'New Description'
        );

        $this->assertGreaterThan(1, $updatedId);

        $category = new Category($createdId);
        $this->assertEquals('Unit Test New Name', $category->getName());
        $this->assertEquals('New Description', $category->getDescription());
    }

    public function testDeleteCategoryReturnsTrue(): void {
        $name = 'Unit Test ' . time();
        $description = 'Description ' . time();
        $createdId = Categories::addCategory($name, $description);

        $controller = new CategoryController();
        $success = $controller->_deletePost($createdId);

        $this->assertTrue($success);

        $category = new Category($createdId);
        $this->assertNull($category->getID());
    }

    // can't delete "General" category
    public function testDeleteCategoryReturnsFalse(): void {
        $controller = new CategoryController();
        $success = $controller->_deletePost(1);

        $this->assertFalse($success);

        $category = new Category(1);
        $this->assertEquals(1, $category->getID());
    }
}
