<?php

class VideoPageTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
    }

    public function testSetArticle(): void {
        $videoPage = new Video_Page();
        $videoPage->title = 'Unit Test Article';
        $videoPage->slug = 'unit test';
        $videoPage->menu = 'none';
        $videoPage->language = 'de';
        $videoPage->author_id = 1;
        $videoPage->group_id = 1;
        $videoPage->update();

        $this->assertNotNull($videoPage->getId());
    }
}
