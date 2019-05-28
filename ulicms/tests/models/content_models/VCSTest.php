<?php

use UliCMS\Models\Content\VCS;

class VCSTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::deleteFrom("content", "slug like 'unit-test%");
        Database::deleteFrom("history", "content like ' %Text'");
    }

    public function testGetRevisionByContentId() {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Old Text";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        VCS::createRevision($page->getID(), "New Text", 1);
        $revisions = VCS::getRevisionsByContentID($page->getID());
        $firstRevision = $revisions[0];

        $this->assertEquals("New Text", $firstRevision->content);
        $this->assertEquals($page->id, $firstRevision->content_id);
        $this->assertEquals(1, $firstRevision->user_id);

        $revision = VCS::getRevisionByID($firstRevision->id);

        $this->assertEquals("New Text", $revision->content);
        $this->assertEquals($page->id, $revision->content_id);
        $this->assertEquals(1, $revision->user_id);
    }

}
