<?php

class ModulePageTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
    }

    public function testContainsModuleReturnsTrue() {
        $modulePage = new Module_Page();
        $modulePage->title = "Unit Test Article";
        $modulePage->slug = "unit test";
        $modulePage->menu = "none";
        $modulePage->language = "de";
        $modulePage->article_date = 1413821696;
        $modulePage->author_id = 1;
        $modulePage->group_id = 1;
        $modulePage->module = 'blog';
        $modulePage->save();
        $id = $modulePage->id;

        $modulePage = ContentFactory::getByID($id);

        $this->assertTrue($modulePage->containsModule());
        $this->assertTrue($modulePage->containsModule("blog"));
        $this->assertFalse($modulePage->containsModule("not_existing"));
    }

    public function testContainsModuleReturnFalse() {
        $modulePage = new Module_Page();
        $modulePage->title = "Unit Test Article";
        $modulePage->slug = "unit test";
        $modulePage->menu = "none";
        $modulePage->language = "de";
        $modulePage->article_date = 1413821696;
        $modulePage->author_id = 1;
        $modulePage->group_id = 1;
        $modulePage->save();
        $this->assertFalse($modulePage->containsModule());
        $this->assertFalse($modulePage->containsModule("blog"));
    }

}
