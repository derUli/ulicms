<?php

use App\Models\Content\TypeMapper;
use App\Exceptions\UnknownContentTypeException;
use App\Models\Content\Comment;
use App\Models\Content\Category;
use App\Registries\LoggerRegistry;

class ContentFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        LoggerRegistry::register(
            "exception_log",
            new Logger(Path::resolve("ULICMS_LOG/exception_log"))
        );
    }

    protected function tearDown(): void
    {
        Database::deleteFrom("content", "type = 'gibts_nicht' or slug like 'unit-test-%'");
        Database::deleteFrom("categories", "name like 'The Test%'");
        Database::deleteFrom("users", "username like 'testuser%'");

        LoggerRegistry::unregister("exception_log");
    }

    public function testGetAllbyType()
    {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $content = ContentFactory::getAllByType($type);
            foreach ($content as $page) {
                $this->assertInstanceOf($modelClass, $page);
            }
        }
    }

    public function testGetAllByLanguage()
    {
        $languages = getAllLanguages();

        foreach ($languages as $language) {
            $content = ContentFactory::getAllByLanguage($language);
            foreach ($content as $page) {
                $this->assertEquals($language, $page->language);
            }
        }
    }

    public function testGetAllbyMenu()
    {
        $menus = get_all_menus();

        foreach ($menus as $menu) {
            $content = ContentFactory::getAllByMenu($menu);
            foreach ($content as $page) {
                $this->assertEquals($menu, $page->menu);
            }
        }
    }

    public function testThrowsExceptionOnUnknownTypes()
    {
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
            "Content with id={$page->getId()} has unknown content type \"{$page->type}\""
        );

        ContentFactory::getBySlugAndLanguage("test-123", 'de');
    }

    public function testGetAllByParent()
    {
        $result = Database::pQuery("select parent_id from {prefix}content where "
                        . "parent_id is not null", [], true);
        $dataset = Database::fetchObject($result);

        $pages = ContentFactory::getAllByParent($dataset->parent_id);

        $this->assertGreaterThanOrEqual(1, count($pages));
        foreach ($pages as $page) {
            $this->assertEquals($dataset->parent_id, $page->parent_id);
        }
    }

    public function testGetAllByParentNoParent()
    {
        $pages = ContentFactory::getAllByParent(null);

        $this->assertGreaterThanOrEqual(1, count($pages));
        foreach ($pages as $page) {
            $this->assertNull($page->parent_id);
        }
    }

    public function testGetAll()
    {
        $content = ContentFactory::getAll();
        $result = Database::pQuery("select id from {prefix}content", [], true);
        $this->assertEquals(count($content), Database::getNumRows($result));

        foreach ($content as $page) {
            $this->assertInstanceOf(Content::class, $page);
        }
    }

    public function testGetAllRegular()
    {
        $content = ContentFactory::getAllRegular();

        foreach ($content as $page) {
            $this->assertTrue($page->isRegular());
        }
    }

    public function testFilterByEnabled()
    {
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

    public function testGetAllWithComments()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $comment = new Comment();
        $comment->setContentId($page->id);
        $comment->setAuthorName("John Doe");
        $comment->setAuthorEmail("john@doe.de");
        $comment->setAuthorUrl("http://john-doe.de");
        $comment->setIp("123.123.123.123");
        $comment->setUserAgent("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36");
        $comment->setText("Unit Test 1");
        $comment->setRead(true);

        $time = time();
        $comment->setDate($time);

        $comment->save();

        $this->assertGreaterThanOrEqual(1, ContentFactory::getAllWithComments());
    }

    public function testFilterByCategory()
    {
        $category1 = new Category();
        $category1->setName("The Test 1 ");
        $category1->save();

        $category2 = new Category();
        $category2->setName("The Test 2");
        $category2->save();

        $contentDatasets = [];

        for ($i = 1; $i <= 7; $i++) {
            $page = new Page();
            $page->title = 'Unit Test ' . time();
            $page->slug = 'unit-test-' . time() . "-$i";
            $page->language = 'de';
            $page->content = "Some Text";
            $page->comments_enabled = true;
            $page->author_id = 1;
            $page->group_id = 1;
            $page->category_id = $category1->getId();
            $page->save();
            $contentDatasets[] = $page;
        }

        for ($i = 1; $i <= 3; $i++) {
            $page = new Page();
            $page->title = 'Unit Test ' . time();
            $page->slug = 'unit-test-' . time() . "-$i-zwei";
            $page->language = 'de';
            $page->content = "Some Text";
            $page->comments_enabled = true;
            $page->author_id = 1;
            $page->group_id = 1;
            $page->category_id = $category2->getId();
            $page->save();
            $contentDatasets[] = $page;
        }

        $this->assertCount(10, $contentDatasets);

        $filteredContent = ContentFactory::filterByCategory($contentDatasets, $category1->getId());
        $this->assertCount(7, $filteredContent);

        $filteredContent = ContentFactory::filterByCategory($contentDatasets, $category2->getId());
        $this->assertCount(3, $filteredContent);
    }

    public function testFilterByAutor()
    {
        $testUser1 = new User();
        $testUser1->setUsername("testuser1");
        $testUser1->setLastname("Doe");
        $testUser1->setFirstname("John");
        $testUser1->setPassword("foobar");
        $testUser1->save();

        $testUser2 = new User();
        $testUser2->setUsername("testuser2");
        $testUser2->setLastname("Doe");
        $testUser2->setFirstname("Jane");
        $testUser2->setPassword("foobar");
        $testUser2->save();

        $contentDatasets = [];

        for ($i = 1; $i <= 8; $i++) {
            $page = new Page();
            $page->title = 'Unit Test ' . time();
            $page->slug = 'unit-test-' . time() . "-$i";
            $page->language = 'de';
            $page->content = "Some Text";
            $page->comments_enabled = true;
            $page->author_id = $testUser1->getId();
            $page->group_id = 1;
            $page->category_id = 1;
            $page->save();
            $contentDatasets[] = $page;
        }

        for ($i = 1; $i <= 4; $i++) {
            $page = new Page();
            $page->title = 'Unit Test ' . time();
            $page->slug = 'unit-test-' . time() . "-$i-zwei";
            $page->language = 'de';
            $page->content = "Some Text";
            $page->comments_enabled = true;
            $page->author_id = $testUser2->getId();
            $page->group_id = 1;
            $page->category_id = 1;
            $page->save();
            $contentDatasets[] = $page;
        }

        $this->assertCount(12, $contentDatasets);

        $filteredContent = ContentFactory::filterByAuthor($contentDatasets, $testUser1->getId());
        $this->assertCount(8, $filteredContent);

        $filteredContent = ContentFactory::filterByAuthor($contentDatasets, $testUser2->getId());
        $this->assertCount(4, $filteredContent);
    }

    public function testFilterByLastChangeBy()
    {
        $testUser1 = new User();
        $testUser1->setUsername("testuser1");
        $testUser1->setLastname("Doe");
        $testUser1->setFirstname("John");
        $testUser1->setPassword("foobar");
        $testUser1->save();

        $testUser2 = new User();
        $testUser2->setUsername("testuser2");
        $testUser2->setLastname("Doe");
        $testUser2->setFirstname("Jane");
        $testUser2->setPassword("foobar");
        $testUser2->save();

        $contentDatasets = [];

        for ($i = 1; $i <= 5; $i++) {
            $page = new Page();
            $page->title = 'Unit Test ' . time();
            $page->slug = 'unit-test-' . time() . "-$i";
            $page->language = 'de';
            $page->content = "Some Text";
            $page->comments_enabled = true;
            $page->author_id = $testUser1->getId();
            $page->lastchangeby = $testUser1->getId();
            $page->group_id = 1;
            $page->category_id = 1;
            $page->save();
            $contentDatasets[] = $page;
        }

        for ($i = 1; $i <= 3; $i++) {
            $page = new Page();
            $page->title = 'Unit Test ' . time();
            $page->slug = 'unit-test-' . time() . "-$i-zwei";
            $page->language = 'de';
            $page->content = "Some Text";
            $page->comments_enabled = true;
            $page->author_id = $testUser2->getId();
            $page->lastchangeby = $testUser2->getId();
            $page->group_id = 1;
            $page->category_id = 1;
            $page->save();
            $contentDatasets[] = $page;
        }

        $this->assertCount(8, $contentDatasets);

        $filteredContent = ContentFactory::filterByLastChangeBy($contentDatasets, $testUser1->getId());
        $this->assertCount(5, $filteredContent);

        $filteredContent = ContentFactory::filterByLastChangeBy($contentDatasets, $testUser2->getId());
        $this->assertCount(3, $filteredContent);
    }

    public function testGetForFilter()
    {
        $contents = ContentFactory::getForFilter(
            'de',
            1,
            "top",
            5,
            "title",
            "asc",
            "module",
            4
        );

        $this->assertIsArray($contents);
        $this->assertLessThanOrEqual(4, count($contents));

        foreach ($contents as $content) {
            $this->assertEquals('de', $content->language);
            $this->assertEquals("top", $content->menu);
            $this->assertInstanceOf(Module_Page::class, $content);
        }
    }
}
