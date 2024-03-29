<?php

use App\Models\Content\Categories;
use App\Models\Content\Category;

class CategoriesTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::query(
            'delete from {prefix}categories '
            . "where name like 'Test Category %' ",
            true
        );
    }

    public function testAddUpdateAndDeleteCategory(): void {
        $name = 'Test Category ' . uniqid();
        $description = uniqid();
        $id = Categories::addCategory($name, $description);
        $this->assertGreaterThanOrEqual(1, $id);

        $newName = 'Test Category ' . uniqid();
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

    public function testGetAllCategories(): void {
        $categories = Categories::getAllCategories('id');
        $this->assertGreaterThanOrEqual(0, count($categories));
        foreach ($categories as $category) {
            $this->assertInstanceOf(Category::class, $category);
        }

        $this->assertEquals('Allgemein', $categories[0]->getName());
    }

    public function testGetHTMLSelectWithAllowNullWithoutDefault(): void {
        $this->assertStringContainsString(
            "<option value='0' selected='selected'>[" .
            get_translation('every') . ']</option>',
            Categories::getHTMLSelect(0, true)
        );
    }

    public function testGetHTMLSelectWithAllowNullWithDefault(): void {
        $this->assertStringContainsString(
            "<option value='0'>[" . get_translation('every') .
            ']</option>',
            Categories::getHTMLSelect(1, true)
        );
    }

    public function testGetHTMLSelectWithCustomFieldName(): void {
        $this->assertStringContainsString(
            "<select name='my_field_name' id='my_field_name' size='1'",
            Categories::getHTMLSelect(1, true, 'my_field_name')
        );
    }

    public function testGetHTMLSelectWithoutCustomFieldName(): void {
        $this->assertStringContainsString(
            "<select name='category_id' id='category_id' size='1'",
            Categories::getHTMLSelect(1, true)
        );
    }
}
