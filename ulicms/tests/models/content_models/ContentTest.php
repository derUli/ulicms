<?php

use UliCMS\Models\Content\TypeMapper;

class ContentTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Content::emptyTrash();
    }

    public function testEmptyTrash() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $page->delete();

        $deleted = Content::getAllDatasets("content", "Page", "id", "deleted_at is not null");
        $this->assertGreaterThanOrEqual(1, count($deleted));

        Content::emptyTrash();

        $deleted = Content::getAllDatasets("content", "Page", "id", "deleted_at is not null");
        $this->assertCount(0, $deleted);
    }

    public function testGetChildrenWithoutId() {
        $page = new Page();
        $this->assertCount(0, $page->getChildren());
    }

    public function testGetIcon() {
        $types = TypeMapper::getMappings();
        foreach ($types as $type => $class) {
            $model = new $class;
            $this->assertStringContainsString("fa", $model->getIcon());
        }
    }

}
