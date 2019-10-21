<?php

use UliCMS\Models\Content\TypeMapper;
use UliCMS\Exceptions\UnknownContentTypeException;

class ContentFactoryTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        LoggerRegistry::register(
                "exception_log",
                new Logger(Path::resolve("ULICMS_LOG/exception_log"))
        );
    }

    public function tearDown() {
        Database::deleteFrom("content", "type = 'gibts_nicht'");
        LoggerRegistry::unregister("exception_log");
    }

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

    public function testThrowsExceptionOnUnknownTypes() {
        $userManager = new UserManager();
        $user = $userManager->getAllUsers()[0];

        $group = Group::getAll()[0];

        $page = new Page();

        $page->type = "gibts_nicht";
        $page->position = 0;
        $page->language = 'de';
        $page->slug = 'test-123';
        $page->title = 'test123';
        $page->menu = 'top';
        $page->content = '';
        $page->author_id = $user->getId();
        $page->group_id = $group->getId();

        $page->save();

        $this->expectException(UnknownContentTypeException::class);
        $this->expectExceptionMessage(
                "Content with id={$page->getId()} has unknown content type \"{$page->type}\"");

        ContentFactory::getBySlugAndLanguage("test-123", "de");
    }

    public function testGetAllByParent() {

        $result = Database::pQuery("select parent_id from {prefix}content where "
                        . "parent_id is not null", [], true);
        $dataset = Database::fetchObject($result);

        $pages = ContentFactory::getAllByParent($dataset->parent_id);

        $this->assertGreaterThanOrEqual(1, count($pages));
        foreach ($pages as $page) {
            $this->assertEquals($dataset->parent_id, $page->parent_id);
        }
    }

    public function testGetAllByParentNoParent() {
        $pages = ContentFactory::getAllByParent(null);

        $this->assertGreaterThanOrEqual(1, count($pages));
        foreach ($pages as $page) {
            $this->assertNull($page->parent_id);
        }
    }

    public function testGetAll() {
        $content = ContentFactory::getAll();
        $result = Database::pQuery("select id from {prefix}content", [], true);
        $this->assertEquals(count($content), Database::getNumRows($result));

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
        $elements = [];

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
