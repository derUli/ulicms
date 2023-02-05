<?php

use UliCMS\Models\Content\VCS;

class HistoryControllerTest extends \PHPUnit\Framework\TestCase {

    protected function tearDown(): void {
        Database::deleteFrom("content", "slug like 'unit-test-%'");
    }

    public function testDoRestoreReturnsRevision() {
        $testPage = $this->createTestPage();
        $this->createHistories($testPage);

        $revisions = VCS::getRevisionsByContentID($testPage->getID(), "id desc");
        $this->assertCount(3, $revisions);
        $testPage->reload();
        $this->assertEquals($testPage->content, "Some Text 3");

        $historyController = new HistoryController();
        $restoredRevision = $historyController->_doRestore($revisions[1]->id);

        $this->assertEquals($testPage->id, intval($restoredRevision->content_id));
        $this->assertEquals($revisions[1]->id, intval($restoredRevision->id));
        $this->assertGreaterThan(1590795228, strtotime($restoredRevision->date));
        $this->assertEquals("Some Text 2", $restoredRevision->content);
        $this->assertGreaterThanOrEqual(1, $restoredRevision->user_id);

        $testPage->reload();
        $this->assertEquals($testPage->content, "Some Text 2");
    }

    public function testDoRestoreReturnsNull() {
        $historyController = new HistoryController();
        $restoredRevision = $historyController->_doRestore(PHP_INT_MAX);
        $this->assertNull($restoredRevision);
    }

    public function getTestUser(): User {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];
        $user->save();
        return $user;
    }

    private function createTestPage(): Page {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text 1";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        return $page;
    }

    private function createHistories(Page $page) {
        $testUser = $this->getTestUser();
        VCS::createRevision($page->getID(), $page->content, $testUser->getId());
        $page->content = "Some Text 2";
        $page->save();
        VCS::createRevision($page->getID(), $page->content, $testUser->getId());

        $page->content = "Some Text 3";
        $page->save();
        VCS::createRevision($page->getID(), $page->content, $testUser->getId());
    }

}
