<?php

class ContentFactoryTest extends \PHPUnit\Framework\TestCase {

    public function testGetAllbyType() {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $content = ContentFactory::getAllByType($types);
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

}
