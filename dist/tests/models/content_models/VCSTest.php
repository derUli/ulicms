<?php

use App\Models\Content\VCS;

class VCSTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        Database::deleteFrom("content", "slug like '%unit-test-%'");
        Database::deleteFrom("history", "content like '%Text%'");
    }

    public function testGetRevisionByContentIdReturnsRevision()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Old Text 1";
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

    public function testGetRevisionByIdReturnsNull()
    {
        $this->assertNull(VCS::getRevisionByID(PHP_INT_MAX));
    }

    public function testRestoreRevisionNotExistingReturnsFalse()
    {
        $this->assertFalse(VCS::restoreRevision(PHP_INT_MAX));
    }

    public function testRestoreRevisionReturnsTrue()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Old Text 1";
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        VCS::createRevision($page->getID(), "New Text 1", 1);
        VCS::createRevision($page->getID(), "New Text 2", 1);
        $revisions = VCS::getRevisionsByContentID($page->getID(), "id asc");
        $lastRevision = $revisions[1];

        $page = new Page($page->getID());
        $this->assertNotEquals("New Text 2", $page->content);
        $this->assertTrue(VCS::restoreRevision($lastRevision->id));
        $page = new Page($page->getID());
        $this->assertEquals("New Text 2", $page->content);
    }
}
