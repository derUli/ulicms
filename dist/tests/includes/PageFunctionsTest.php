<?php


class PageFunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testGetPageSlugByID()
    {
        $allPages = ContentFactory::getAll();
        $first = $allPages[0];
        $this->assertEquals($first->slug, getPageSlugByID($first->id));
        $this->assertNull(getPageSlugByID(PHP_INT_MAX));
    }

    public function testGetPageTitleByID()
    {
        $allPages = ContentFactory::getAll();
        $first = $allPages[0];
        $this->assertEquals($first->title, getPageTitleByID($first->id));
        $this->assertEquals('[' . get_translation('none') . ']', getPageTitleByID(PHP_INT_MAX));
    }

    public function testGetPageByIDReturnsNull()
    {
        $this->assertNull(getPageById(PHP_INT_MAX));
    }

    public function testGetPageByIDReturnsObject()
    {
        $all = ContentFactory::getAll();
        $first = $all[0];
        $page = getPageByID($first->id);

        $this->assertIsObject($page);
        $this->assertEquals($first->getId(), $page->id);
        $this->assertEquals($first->title, $page->title);
    }

    public function testGetAllPagesWithTitle()
    {
        $pages = getAllPagesWithTitle();
        $this->assertGreaterThanOrEqual(1, count($pages));
        foreach ($pages as $page) {
            $this->assertCount(2, $page);
            $this->assertNotEmpty($page[0]);
            $this->assertNotEmpty($page[1]);
        }
    }

    public function testGetAllSlugs()
    {
        $slugs = getAllSlugs();
        $this->assertTrue(in_array('willkommen', $slugs));
        $this->assertTrue(in_array('welcome', $slugs));
        $this->assertTrue(in_array('lorem_ipsum', $slugs));
    }

    public function testGetAllSlugsByLanguage()
    {
        $germanSlugs = getAllSlugs('de');
        $this->assertTrue(in_array('willkommen', $germanSlugs));
        $this->assertFalse(in_array('welcome', $germanSlugs));
        $this->assertTrue(in_array('glueckskeks', $germanSlugs));
        $this->assertFalse(in_array('fortune', $germanSlugs));

        $englishSlugs = getAllSlugs('en');
        $this->assertTrue(in_array('welcome', $englishSlugs));
        $this->assertFalse(in_array('willkommen', $englishSlugs));
        $this->assertTrue(in_array('fortune', $englishSlugs));
        $this->assertFalse(in_array('glueckskeks', $englishSlugs));
    }

    public function testGetAllPagesWithHashLinks()
    {
        $pages = getAllPages(null, 'slug', false);
        $this->assertGreaterThan(0, count($pages));

        $hasHashhLinks = false;
        foreach ($pages as $page) {
            $content = ContentFactory::getById($page['id']);
            $this->assertInstanceOf(AbstractContent::class, $content);
            $this->assertIsNumeric($content->getId());
            if (! $content->isRegular()) {
                $hasHashhLinks = true;
            }
        }

        $this->assertTrue($hasHashhLinks);
    }

    public function testGetAllPagesWithoutHashLinks()
    {
        $allPages = getAllPages(null, 'slug', false);
        $pages = getAllPages(null, 'slug', true);

        $this->assertGreaterThan(0, count($pages));
        $this->assertLessThan(count($allPages), count($pages));

        foreach ($pages as $page) {
            $content = ContentFactory::getById($page['id']);
            $this->assertTrue($content->isRegular());
        }
    }

    public function testGetAllPagesByLanguage()
    {
        $pages = getAllPages('en', 'id');
        $this->assertGreaterThan(0, count($pages));

        $oldPageId = 0;
        foreach ($pages as $page) {
            $this->assertGreaterThan($oldPageId, $page['id']);
            $oldPageId = $page['id'];
            $this->assertEquals('en', $page['language']);
        }
    }

    public function testGetAllPagesByMenu()
    {
        $pages = getAllPages(null, 'id', false, 'top');
        $this->assertGreaterThan(0, count($pages));

        $oldPageId = 0;
        foreach ($pages as $page) {
            $this->assertGreaterThan($oldPageId, $page['id']);
            $oldPageId = $page['id'];
            $this->assertEquals('top', $page['menu']);
        }
    }

    public function testGetAllPagesByLanguageAndMenu()
    {
        $pages = getAllPages('en', 'id', false, 'top');
        $this->assertGreaterThan(0, count($pages));

        $oldPageId = 0;
        foreach ($pages as $page) {
            $this->assertGreaterThan($oldPageId, $page['id']);
            $oldPageId = $page['id'];
            $this->assertEquals('top', $page['menu']);
            $this->assertEquals('en', $page['language']);
        }
    }
}
