
<?php

use UliCMS\Exceptions\DatabaseException;

class ListDataTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
    }

    public function testFilter() {
        $contentList = $this->createContentList();
        $contents = $contentList->listData->filter();

        $this->assertIsArray($contents);
        $this->assertLessThanOrEqual(4, count($contents));

        foreach ($contents as $content) {
            $this->assertEquals("de", $content->language);
            $this->assertEquals("top", $content->menu);
            $this->assertInstanceOf(Module_Page::class, $content);
        }
    }

    public function testLoadById() {
        $contentList = $this->createContentList();
        $loaded = new List_Data($contentList->getId());

        $this->assertTrue($loaded->isPersistent());
        $this->assertFalse($loaded->hasChanges());

        $this->assertEquals("de", $loaded->language);
        $this->assertEquals(1, $loaded->category_id);
        $this->assertEquals("top", $loaded->menu);
        $this->assertEquals(5, $loaded->parent_id);
        $this->assertTrue($loaded->use_pagination);
        $this->assertEquals(4, $loaded->limit);
        $this->assertEquals("module", $loaded->type);
        $this->assertEquals("id", $loaded->order_by);
        $this->assertEquals("desc", $loaded->order_direction);
    }

    public function testUpdate() {
        $contentList = $this->createContentList();
        $loaded = new List_Data($contentList->getId());
        $loaded->limit = 10;
        $loaded->type = 'page';
        $loaded->save();

        $updated = new List_Data($contentList->getId());
        $this->assertEquals(10, $updated->limit);
        $this->assertEquals("page", $updated->type);
    }

    public function testCreateEmpty() {
        $contentList = new Content_List ();
        $contentList->title = "Unit Test Article";
        $contentList->slug = "unit test";
        $contentList->menu = "none";
        $contentList->language = "de";
        $contentList->article_date = 1413821696;
        $contentList->author_id = 1;
        $contentList->group_id = 1;
        $contentList->save();

        $contentList->listData = $this->getEmptyListData($contentList->getID());

        $loaded = new List_Data($contentList->getId());

        $this->assertNull($loaded->language);
        $this->assertNull($loaded->category_id);
        $this->assertNull($loaded->menu);
        $this->assertNull($loaded->parent_id);
        $this->assertFalse($loaded->use_pagination);
        $this->assertNull($loaded->limit);
        $this->assertNull($loaded->type);
        $this->assertNull($loaded->order_by);
        $this->assertEquals("asc", $loaded->order_direction);
    }

    private function getEmptyListData($contentId) {
        $listData = new List_Data($contentId);
        $listData->order_by = null;

        $listData->save();
        $listData->save();

        return $listData;
    }

    private function createContentList() {
        $contentList = new Content_List ();
        $contentList->title = "Unit Test Article";
        $contentList->slug = "unit test";
        $contentList->menu = "none";
        $contentList->language = "de";
        $contentList->article_date = 1413821696;
        $contentList->author_id = 1;
        $contentList->group_id = 1;
        $contentList->save();

        $contentList->listData = $this->createListData($contentList->id);

        return $contentList;
    }

    private function createListData($contentId) {

        $listData = new List_Data($contentId);
        $listData->language = "de";
        $listData->category_id = 1;
        $listData->menu = "top";
        $listData->parent_id = 5;
        $listData->use_pagination = true;
        $listData->limit = 4;
        $listData->order_by = 'id';
        $listData->order_direction = 'desc';
        $listData->type = 'module';
        $listData->save();

        return $listData;
    }

    public function testSaveWithoutContentId() {
        $this->expectException(DatabaseException::class);

        $listData = new List_Data();
        $listData->save();
    }

}
