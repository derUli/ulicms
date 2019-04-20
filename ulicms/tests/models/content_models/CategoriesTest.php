<?php

class CategoriesTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::query(
                "delete from {prefix}categories "
                . "where name like 'Test Category %' ", true);
    }

    public function testAddUpdateAndDeleteCategory() {
        $name = "Test Category " . uniqid();
        $description = uniqid();
        $id = Categories::addCategory($name, $description);
        $this->assertGreaterThanOrEqual(1, $id);


        $newName = "Test Category " . uniqid();
        $newDescription = uniqid();

        $this->assertEquals($name, Categories::getCategoryById($id));
        $this->assertEquals($description, Categories::getCategoryDescriptionById($id));

        Categories::updateCategory($id, $newName, $newDescription);

        $this->assertEquals($newName, Categories::getCategoryById($id));
        $this->assertEquals($newDescription, Categories::getCategoryDescriptionById($id));

        Categories::deleteCategory($id);

        $this->assertNull(Categories::getCategoryById($id));
        $this->assertNull(Categories::getCategoryDescriptionById($id));
    }

}
