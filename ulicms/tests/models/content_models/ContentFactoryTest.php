<?php

class ContentFactoryTest extends \PHPUnit\Framework\TestCase {

    public function testGetAllbyType() {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $content = ContentFactory::getAllByType($type);
            foreach ($content as $page) {
                $this->assertInstanceOf($modelClass, $page);
            }
        }
    }

    public function testGetAllByLanguage() {
        $languages = getAllLanguages();

        foreach ($languages as $language) {
            $content = ContentFactory::getAllByLanguage($language);
            foreach ($content as $page) {
                $this->assertEquals($language, $page->language);
            }
        }
    }

    public function testGetAllbyMenu() {
        $menus = getAllMenus();

        foreach ($menus as $menu) {
            $content = ContentFactory::getAllByMenu($menu);
            foreach ($content as $page) {
                $this->assertEquals($menu, $page->menu);
            }
        }
    }

    public function testGetAll() {
        $content = ContentFactory::getAll();
        $query = Database::pQuery("select id from {prefix}content", array(), true);
        $this->assertEquals(count($content), Database::getNumRows($query));

        foreach ($content as $page) {
            $this->assertInstanceOf(Content::class, $page);
        }
    }

    public function testGetAllRegular() {
        $content = ContentFactory::getAllRegular();

        foreach ($content as $page) {
            $this->assertTrue($page->isRegular());
        }
    }

    public function testFilterByEnabled() {
        $elements = array();

        $test1 = new Page();
        $test1->active = 1;
        $elements[] = $test1;

        $test2 = new Page();
        $test2->active = 1;
        $elements[] = $test2;


        $test5 = new Page();
        $test5->active = 1;
        $elements[] = $test5;

        $test3 = new Page();
        $test3->active = 0;

        $elements[] = $test3;

        $test4 = new Page();
        $test4->active = 0;
        $elements[] = $test4;

        $enabled = ContentFactory::filterByEnabled($elements, true);
        $this->assertCount(3, $enabled);

        foreach ($enabled as $element) {
            $this->assertEquals(1, $element->active);
        }


        $disabled = ContentFactory::filterByEnabled($elements, false);
        $this->assertCount(2, $disabled);

        foreach ($disabled as $element) {
            $this->assertEquals(0, $element->active);
        }
    }

}
