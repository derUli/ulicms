
<?php

use App\Exceptions\DatabaseException;
use App\Models\Content\Category;

class ListDataTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
        Database::query("delete from {prefix}categories where name = 'Test Category'", true);
    }

    public function testFilter(): void {
        $contentList = $this->createContentList();
        $contents = $contentList->listData->filter();

        $this->assertIsArray($contents);
        $this->assertLessThanOrEqual(4, count($contents));

        foreach ($contents as $content) {
            $this->assertEquals('de', $content->language);
            $this->assertEquals('top', $content->menu);
            $this->assertInstanceOf(Module_Page::class, $content);
        }
    }

    public function createTestContent($category): void {
        for ($i = 1; $i <= 20; $i++) {
            $page = new Page();
            $page->category_id = $category->getId();
            $page->title = "Unit Test {$i}";
            $page->slug = uniqid();
            $page->menu = 'top';
            $page->language = 'de';
            $page->content = 'foo [csrf_token_html] bar';
            $page->author_id = 1;
            $page->group_id = 1;
            $page->content = 'foo';
            $page->save();
        }
    }

    public function testFilterAll(): void {
        $category = new Category();
        $category->setName('Test Category');
        $category->save();

        $this->createTestContent($category);

        $contentList = $this->createContentList();
        $listData = new List_Data($contentList->getId());
        $listData->language = 'de';
        $listData->limit = 5;
        $listData->category_id = $category->getID();
        $listData->use_pagination = true;
        $listData->menu = 'top';
        $listData->parent_id = null;
        $listData->type = null;
        $listData->save();

        $contents = $listData->filterAll();
        $this->assertEquals(20, count($contents));
    }

    public function testFilterHasMoreReturnsTrue(): void {
        $category = new Category();
        $category->setName('Test Category');
        $category->save();

        $this->createTestContent($category);

        $contentList = $this->createContentList();
        $listData = new List_Data($contentList->getId());
        $listData->language = 'de';
        $listData->limit = 5;
        $listData->category_id = $category->getID();
        $listData->use_pagination = true;
        $listData->menu = 'top';
        $listData->parent_id = null;
        $listData->type = null;
        $listData->save();

        // current page 1 - 5
        // next page 6 - 10
        $this->assertTrue($listData->hasMore(0));
        // current page 6 - 10
        // next page 11 - 15
        $this->assertTrue($listData->hasMore(5));
        // current page 10 - 15
        // next page 16 - 20
        $this->assertTrue($listData->hasMore(10));

        // current page 14 - 19
        // Next Page 20
        $this->assertTrue($listData->hasMore(14));
    }

    public function testFilterHasMoreReturnsFalse(): void {
        $category = new Category();
        $category->setName('Test Category');
        $category->save();

        $this->createTestContent($category);

        $contentList = $this->createContentList();
        $listData = new List_Data($contentList->getId());
        $listData->language = 'de';
        $listData->limit = 5;
        $listData->category_id = $category->getID();
        $listData->use_pagination = true;
        $listData->menu = 'top';
        $listData->parent_id = null;
        $listData->type = null;
        $listData->save();

        // current page 15 - 20
        // next page is empty since there are only 20 datasets for this filter
        $this->assertFalse($listData->hasMore(15));
        $this->assertFalse($listData->hasMore(20));
    }

    public function testFilterPaginated(): void {
        $category = new Category();
        $category->setName('Test Category');
        $category->save();

        $this->createTestContent($category);

        $contentList = $this->createContentList();
        $listData = new List_Data($contentList->getId());
        $listData->language = 'de';
        $listData->limit = 5;
        $listData->category_id = $category->getID();
        $listData->use_pagination = true;
        $listData->menu = 'top';
        $listData->parent_id = null;
        $listData->type = null;
        $listData->order_by = 'id';
        $listData->order_direction = 'asc';
        $listData->save();

        $pagination1 = $listData->filterPaginated(0);
        $pagination2 = $listData->filterPaginated(5);
        $pagination3 = $listData->filterPaginated(10);
        $pagination4 = $listData->filterPaginated(15);
        $pagination5 = $listData->filterPaginated(18);
        $pagination6 = $listData->filterPaginated(20);

        $this->assertCount(5, $pagination1);
        $this->assertCount(5, $pagination2);
        $this->assertCount(5, $pagination3);
        $this->assertCount(5, $pagination4);
        $this->assertCount(2, $pagination5);
        $this->assertCount(0, $pagination6);

        $this->assertEquals('Unit Test 1', $pagination1[0]->title);
        $this->assertEquals('Unit Test 6', $pagination2[0]->title);
        $this->assertEquals('Unit Test 11', $pagination3[0]->title);
        $this->assertEquals('Unit Test 16', $pagination4[0]->title);
        $this->assertEquals('Unit Test 19', $pagination5[0]->title);
    }

    public function testLoadById(): void {
        $contentList = $this->createContentList();
        $loaded = new List_Data($contentList->getId());

        $this->assertTrue($loaded->isPersistent());
        $this->assertFalse($loaded->hasChanges());

        $this->assertEquals('de', $loaded->language);
        $this->assertEquals(1, $loaded->category_id);
        $this->assertEquals('not_in_menu', $loaded->menu);
        $this->assertEquals(5, $loaded->parent_id);
        $this->assertTrue($loaded->use_pagination);
        $this->assertEquals(4, $loaded->limit);
        $this->assertEquals('module', $loaded->type);
        $this->assertEquals('id', $loaded->order_by);
        $this->assertEquals('desc', $loaded->order_direction);
    }

    public function testUpdate(): void {
        $contentList = $this->createContentList();
        $loaded = new List_Data($contentList->getId());
        $loaded->limit = 10;
        $loaded->type = 'page';
        $loaded->save();

        $updated = new List_Data($contentList->getId());
        $this->assertEquals(10, $updated->limit);
        $this->assertEquals('page', $updated->type);
    }

    public function testCreateEmpty(): void {
        $contentList = new Content_List();
        $contentList->title = 'Unit Test Article';
        $contentList->slug = 'unit test';
        $contentList->menu = 'none';
        $contentList->language = 'de';
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
        $this->assertEquals('asc', $loaded->order_direction);
    }

    public function testSaveWithoutContentId(): void {
        $this->expectException(DatabaseException::class);

        $listData = new List_Data();
        $listData->save();
    }

    private function getEmptyListData($contentId) {
        $listData = new List_Data($contentId);
        $listData->order_by = null;

        $listData->save();
        $listData->save();

        return $listData;
    }

    private function createContentList() {
        $contentList = new Content_List();
        $contentList->title = 'Unit Test Article';
        $contentList->slug = 'unit test';
        $contentList->menu = 'none';
        $contentList->language = 'de';
        $contentList->article_date = 1413821696;
        $contentList->author_id = 1;
        $contentList->group_id = 1;
        $contentList->save();

        $contentList->listData = $this->createListData($contentList->id);

        return $contentList;
    }

    private function createListData($contentId) {
        $listData = new List_Data($contentId);
        $listData->language = 'de';
        $listData->category_id = 1;
        $listData->menu = 'not_in_menu';
        $listData->parent_id = 5;
        $listData->use_pagination = true;
        $listData->limit = 4;
        $listData->order_by = 'id';
        $listData->order_direction = 'desc';
        $listData->type = 'module';
        $listData->save();

        return $listData;
    }
}
