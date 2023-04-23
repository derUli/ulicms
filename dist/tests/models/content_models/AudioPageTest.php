<?php

class AudioPageTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
    }

    public function testSetArticle() {
        $audioPage = new Audio_Page();
        $audioPage->title = 'Unit Test Article';
        $audioPage->slug = 'unit test';
        $audioPage->menu = 'none';
        $audioPage->language = 'de';
        $audioPage->article_date = 1413821696;
        $audioPage->author_id = 1;
        $audioPage->group_id = 1;
        $audioPage->update();

        $this->assertNotNull($audioPage->getId());
    }
}
