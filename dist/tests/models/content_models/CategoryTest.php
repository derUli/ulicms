<?php

use App\Models\Content\Category;

class CategoryTest extends \PHPUnit\Framework\TestCase {
    public const name1 = 'Name 1';

    public const name2 = 'Name 2';

    public const description1 = 'Description 1';

    public const description2 = 'Description 2';

    protected function setUp(): void {
        Database::pQuery('delete from `{prefix}categories`
							where name = ? or name = ?', [
            self::name1,
            self::name2
        ], true);
    }

    protected function tearDown(): void {
        $this->setUp();
    }

    public function testCreateEditAndDeleteCategory(): void {
        $category = new Category();
        $category->setName(self::name1);
        $category->setDescription(self::description1);
        $category->save();
        $id = $category->getID();
        $this->assertNotNull($id);
        $category = new Category($id);
        $this->assertEquals(self::name1, $category->getName());
        $this->assertEquals(self::description1, $category->getDescription());
        $this->assertEquals($id, $category->getID());
        $category->setName(self::name2);
        $category->setDescription(self::description2);
        $category->save();
        $category = new Category($id);
        $this->assertEquals(self::name2, $category->getName());
        $this->assertEquals(self::description2, $category->getDescription());
        $category->delete();
        $this->assertNull($category->getID());
        $category = new Category($id);

        $this->assertNull($category->getID());
    }

    public function testSetId(): void {
        $category = new Category();
        $category->setID(123);

        $this->assertEquals(123, $category->getID());
    }
}
