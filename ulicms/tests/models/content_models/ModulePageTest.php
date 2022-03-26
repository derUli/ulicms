<?php

use UliCMS\Packages\Modules\Module;

class ModulePageTest extends \PHPUnit\Framework\TestCase {

    protected function tearDown(): void {
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
        $modulePage->update();
        $this->assertFalse($modulePage->containsModule());
        $this->assertFalse($modulePage->containsModule("blog"));
    }

    public function testGetEmbeddedModulesReturnsModules() {
        $module = new Module("fortune2");

        $modulePage = new Module_Page();
        $modulePage->title = "Unit Test Article";
        $modulePage->slug = "unit test";
        $modulePage->menu = "none";
        $modulePage->language = "de";
        $modulePage->article_date = 1413821696;
        $modulePage->author_id = 1;
        $modulePage->group_id = 1;
        $modulePage->module = 'blog';

        $modulePage->content = $module->getShortCode();
        $modulePage->save();

        $this->assertTrue($modulePage->containsModule("fortune2"));
        $this->assertTrue($modulePage->containsModule("blog"));

        $this->assertEquals(
                [
                    "fortune2",
                    "blog"
                ],
                $modulePage->getEmbeddedModules()
        );
    }

    public function testGetEmbeddedModulesReturnsNothing() {
        $module = new Module("fortune2");

        $modulePage = new Module_Page();
        $modulePage->title = "Unit Test Article";
        $modulePage->slug = "unit test";
        $modulePage->menu = "none";
        $modulePage->language = "de";
        $modulePage->article_date = 1413821696;
        $modulePage->author_id = 1;
        $modulePage->group_id = 1;

        $modulePage->content = "no content";
        $modulePage->save();

        $this->assertFalse($modulePage->containsModule("fortune2"));
        $this->assertFalse($modulePage->containsModule("blog"));

        $this->assertCount(0, $modulePage->getEmbeddedModules());
    }

}
