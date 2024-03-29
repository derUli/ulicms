<?php

use App\Exceptions\DatasetNotFoundException;
use App\Models\Content\Language;
use App\Models\Content\VCS;
use App\Registries\ModelRegistry;
use App\Translations\Translation;
use Spatie\Snapshots\MatchesSnapshots;

class PageControllerTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    protected function setUp(): void {
        $manager = new \App\Packages\ModuleManager();
        $manager->sync();

        ModelRegistry::loadModuleModels();

        require_once getLanguageFilePath('en');
        Translation::loadAllModuleLanguageFiles('en');

        $_SESSION = [];
        $_POST = [];
        $_GET = [];
    }

    protected function tearDown(): void {
        $_POST = [];
        $_GET = [];
        $_SESSION = [];

        $manager = new \App\Models\Users\UserManager();
        $user = $manager->getAllUsers('admin desc')[0];
        $user->setSecondaryGroups([]);
        $user->save();

        Database::deleteFrom('content', "slug like 'unit-test-%'");
    }

    public function testGetPagesListViewNotSetReturnsDefault(): void {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals('default', $controller->_getPagesListView());
    }

    public function testGetPagesListReturnsDefault(): void {
        $_SESSION['pages_list_view'] = 'default';

        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals('default', $controller->_getPagesListView());
    }

    public function testGetPagesListReturnsRecycleBin(): void {
        $_SESSION['pages_list_view'] = 'recycle_bin';

        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals('recycle_bin', $controller->_getPagesListView());
    }

    public function testcheckIfSlugIsFreeReturnsTrue(): void {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertTrue(
            $controller->_checkIfSlugIsFree(uniqid(), 'de', PHP_INT_MAX)
        );
    }

    public function testcheckIfSlugIsFreeWithEmptyReturnsTrue(): void {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertTrue(
            $controller->_checkIfSlugIsFree('', 'de', PHP_INT_MAX)
        );
    }

    public function testcheckIfSlugIsFreeReturnsFalse(): void {
        $allSlugs = getAllSlugs('de');
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertFalse(
            $controller->_checkIfSlugIsFree($allSlugs[0], 'de', PHP_INT_MAX)
        );
    }

    public function testGetBooleanSelection(): void {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getBooleanSelection();
        $this->assertCount(3, $items);

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetCategorySelection(): void {
        $controller = new PageController();

        $items = $controller->_getCategorySelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertGreaterThanOrEqual(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetMenuSelection(): void {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getMenuSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetTypeSelection(): void {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getTypeSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetLanguageSelection(): void {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getLanguageSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetLanguageSelectionGroupAssigned(): void {
        $user = $this->getTestUser();
        $_SESSION['login_id'] = $user->getId();

        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getLanguageSelection();
        $this->assertCount(2, $items);
    }

    public function getTestUser(): User {
        $manager = new \App\Models\Users\UserManager();
        $user = $manager->getAllUsers('admin desc')[0];

        $german = new Language();
        $german->loadByLanguageCode('de');

        $group = new Group();
        $group->setLanguages([$german]);
        $group->setName('test-group-' . uniqid());
        $group->save();

        $user->setSecondaryGroups([$group]);

        $user->save();

        return $user;
    }

    public function testGetParentIds(): void {
        $controller = ControllerRegistry::get(PageController::class);
        $parentIds = $controller->_getParentIds();

        $this->assertGreaterThanOrEqual(4, count($parentIds));

        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetParentIdsWithLanguageAndMenu(): void {
        $controller = ControllerRegistry::get(PageController::class);

        $parentIds = $controller->_getParentIds('en', 'top');

        $this->assertGreaterThanOrEqual(2, count($parentIds));

        $this->assertLessThan(
            count(
                $controller->_getParentIds()
            ),
            count($parentIds)
        );

        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetParentIdsWithRestrictedGroups(): void {
        $controller = ControllerRegistry::get(PageController::class);
        $allIds = $parentIds = $controller->_getParentIds();

        $user = $this->getTestUser();
        $_SESSION['login_id'] = $user->getId();

        $controller = ControllerRegistry::get(PageController::class);

        $parentIds = $controller->_getParentIds();

        $this->assertGreaterThanOrEqual(2, count($parentIds));

        $this->assertLessThan(
            count(
                $allIds
            ),
            count($parentIds)
        );
        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetContentTypes(): void {
        $controller = new PageController();

        $actual = $controller->_getContentTypes();
        $expected = file_get_contents(
            \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/getContentTypes.expected.json')
        );

        $this->assertEquals(normalizeLN($expected), normalizeLN($actual));
    }

    public function testToggleFilters(): void {
        $_SESSION['login_id'] = 666;
        $controller = new PageController();

        $this->assertTrue($controller->_toggleFilters());
        $this->assertFalse($controller->_toggleFilters());
    }

    public function testToggleShowPositions(): void {
        $_SESSION['login_id'] = 666;
        $controller = new PageController();

        $this->assertTrue($controller->_toggleShowPositions());
        $this->assertFalse($controller->_toggleShowPositions());
    }

    public function testPages(): void {
        $controller = new PageController();
        $controller->_pages();
        $this->assertEquals('default', $_SESSION['pages_list_view']);
    }

    public function testRecycleBin(): void {
        $controller = new PageController();
        $controller->_recycleBin();
        $this->assertEquals('recycle_bin', $_SESSION['pages_list_view']);
    }

    public function testGetCKEditorLinkList(): void {
        $controller = new PageController();
        $links = $controller->_getCKEditorLinkList();
        $this->assertGreaterThanOrEqual(1, count($links));

        foreach ($links as $link) {
            $this->assertCount(2, $link);
            $this->assertNotEmpty($link[0]);
        }
    }

    public function testEmptyTrash(): void {
        $this->createDeletedPage();

        $deleted = AbstractContent::getAllDatasets('content', 'Page', 'id', 'deleted_at is not null');
        $this->assertGreaterThanOrEqual(1, count($deleted));

        $controller = new PageController();
        $controller->_emptyTrash();

        $deleted = AbstractContent::getAllDatasets('content', 'Page', 'id', 'deleted_at is not null');
        $this->assertCount(0, $deleted);
    }

    public function testDiffContents(): void {
        $testDiff = $this->createTestDiff();

        $controller = new PageController();
        $diff = $controller->_diffContents(
            $testDiff->history_id,
            $testDiff->content_id
        );

        $this->assertMatchesHtmlSnapshot($diff->html);
        $this->assertEquals(19, strlen($diff->current_version_date));
        $this->assertEquals(19, strlen($diff->old_version_date));
        $this->assertGreaterThanOrEqual(1, $diff->content_id);
        $this->assertGreaterThanOrEqual(1, $diff->history_id);
    }

    public function testValidateInputReturnsErrors(): void {
        $controller = new PageController();
        $errors = $controller->_validateInput();

        $this->assertStringContainsString('<ul>'
                . '<li>The Slug is required</li>'
                . '<li>The Title is required</li>'
                . '<li>The Language is required</li>'
                . '<li>The Position is required</li>'
                . '<li>The Menu is required</li>'
                . '</ul>', $errors);
    }

    public function testValidateInputSlugXSS(): void {
        $_POST['slug'] = '<script>alert("xss")</script';

        $controller = new PageController();
        $errors = $controller->_validateInput();
        $this->assertEquals('String must not contain HTML.', $errors);
    }

    public function testValidateInputOk(): void {
        $_POST['slug'] = 'foo-bar';
        $_POST['title'] = 'Foobar';
        $_POST['position'] = '123';
        $_POST['menu'] = 'top';
        $_POST['language'] = 'de';

        $controller = new PageController();
        $errors = $controller->_validateInput();
        $this->assertNull($errors);
    }

    public function testGetPages(): void {
        $user = $this->getTestUser();
        $_SESSION['login_id'] = $user->getId();

        $_GET = [
            'start' => '0',
            'length' => '5',
            'draw' => '123'
        ];
        $controller = new PageController();

        $response = $controller->_getPages();

        $this->assertCount(5, $response['data']);
        $this->assertEquals(123, $response['draw']);
        $this->assertGreaterThanOrEqual(5, $response['recordsFiltered']);
        $this->assertEquals(
            $response['recordsFiltered'],
            $response['recordsTotal']
        );
    }

    public function testGetParentSelection(): void {
        $parent = ContentFactory::getBySlugAndLanguage('google', 'en');
        $controller = new PageController();
        $output = $controller->_getParentSelection(
            'en',
            'top',
            $parent->getId()
        );

        $this->assertStringContainsString(
            '<option value="all">[All]</option>',
            $output
        );
        $this->assertStringContainsString(
            '<option value="12">Modules</option>',
            $output
        );
        $this->assertGreaterThanOrEqual(3, substr_count($output, '<option'));
    }

    public function testFilterParentPages(): void {
        $parent = ContentFactory::getBySlugAndLanguage('google', 'en');
        $controller = new PageController();

        $output = $controller->_filterParentPages('en', 'top', $parent->getID());
        $this->assertGreaterThanOrEqual(9, substr_count($output, '<option'));
    }

    public function testNextFreeSlugReturnsSlug(): void {
        $controller = new PageController();
        $slug = $controller->_nextFreeSlug('ziemlich-neu', 'de', 0);
        $this->assertEquals('ziemlich-neu', $slug);
    }

    public function testNextFreeSlugReturnsSlugWithSuffix(): void {
        $this->createTestPages();

        $controller = new PageController();
        $slug = $controller->_nextFreeSlug('unit-test', 'de', 0);
        $this->assertEquals('unit-test-4', $slug);
    }

    public function testDeletePageReturnsTrue(): void {
        $pages = $this->createTestPages();

        $controller = new PageController();
        $success = $controller->_deletePost($pages[0]->getID());
        $this->assertTrue($success);
    }

    public function testDeletePageReturnsFalse(): void {
        $controller = new PageController();
        $success = $controller->_deletePost(PHP_INT_MAX);
        $this->assertFalse($success);
    }

    public function testUnDeletePageReturnsTrue(): void {
        $page = $this->createDeletedPage();

        $controller = new PageController();
        $success = $controller->_undeletePost($page->getID());
        $this->assertTrue($success);
    }

    public function testUnDeletePageReturnsFalse(): void {
        $controller = new PageController();
        $success = $controller->_undeletePost(PHP_INT_MAX);
        $this->assertFalse($success);
    }

    public function testCreatePostReturnsModel(): void {
        $testUser = $this->getTestUser();
        $_SESSION['login_id'] = $testUser->getID();

        $_POST['title'] = 'foobar';
        $_POST['slug'] = 'unit-test-foobar';
        $_POST['type'] = 'page';
        $_POST['content'] = '<p>Foo Content</p>';
        $_POST['position'] = '123';
        $_POST['menu'] = 'not_in_menu';
        $_POST['language'] = 'de';

        $controller = new PageController();
        $content = $controller->_createPost();

        $this->assertInstanceOf(AbstractContent::class, $content);
        $this->assertTrue($content->isPersistent());
    }

    public function testCreatePostReturnsNull(): void {
        $_SESSION['login_id'] = PHP_INT_MAX;

        $_POST['title'] = 'foobar';
        $_POST['slug'] = 'unit-test-foobar';
        $_POST['type'] = 'no_type';
        $_POST['content'] = '<p>Foo Content</p>';
        $_POST['position'] = '123';
        $_POST['menu'] = 'not_in_menu';
        $_POST['language'] = 'de';

        $controller = new PageController();
        $content = $controller->_createPost();

        $this->assertNull($content);
    }

    public function testEditPostSuccessReturnsTrue(): void {
        $pages = $this->createTestPages();

        $types = [
            'page',
            'node',
            'module',
            'video',
            'audio',
            'image',
            'article',
            'list'
        ];
        foreach ($types as $type) {
            $testUser = $this->getTestUser();
            $_SESSION['login_id'] = $testUser->getID();

            $_POST['page_id'] = $pages[0]->getId();
            $_POST['title'] = 'foobar';
            $_POST['slug'] = 'unit-test-foobar';
            $_POST['type'] = $type;
            $_POST['content'] = '<p>New Content</p>';
            $_POST['position'] = '123';

            $_POST['article_date'] = App\Helpers\NumberFormatHelper::timestampToSqlDate();

            $_POST['menu'] = 'not_in_menu';
            $_POST['language'] = 'de';
            $_POST['active'] = '1';
            $_POST['access'] = ['all'];

            $controller = new PageController();
            $success = $controller->_editPost();

            $this->assertTrue($success, "saving content of type {$type} failed");
        }
    }

    public function testEditPostNotFoundReturnsFalse(): void {
        $testUser = $this->getTestUser();
        $_SESSION['login_id'] = $testUser->getID();

        $_POST['page_id'] = PHP_INT_MAX;
        $_POST['title'] = 'foobar';
        $_POST['slug'] = 'unit-test-foobar';
        $_POST['type'] = 'page';
        $_POST['content'] = '<p>Foo Content</p>';
        $_POST['position'] = '123';
        $_POST['menu'] = 'not_in_menu';
        $_POST['language'] = 'de';

        $controller = new PageController();
        $success = $controller->_editPost();

        $this->assertFalse($success);
    }

    public function testEditPostInvalidTypeReturnsFalse(): void {
        $testUser = $this->getTestUser();
        $_SESSION['login_id'] = $testUser->getID();

        $_POST['id'] = PHP_INT_MAX;
        $_POST['title'] = 'foobar';
        $_POST['slug'] = 'unit-test-foobar';
        $_POST['type'] = 'magic_content';
        $_POST['content'] = '<p>Foo Content</p>';
        $_POST['position'] = '123';
        $_POST['menu'] = 'not_in_menu';
        $_POST['language'] = 'de';

        $controller = new PageController();
        $success = $controller->_editPost();

        $this->assertFalse($success);
    }

    public function testGetParentPageIdReturnsId(): void {
        $testPages = $this->createTestPages();
        $id = $testPages[3]->getId();

        $controller = new PageController();
        $actual = $controller->_getParentPageId($id);

        $this->assertGreaterThanOrEqual(1, $actual->id);
        $this->assertNotEquals($actual->id, $id);
    }

    public function testGetParentPageIdReturnsNull(): void {
        $page = ContentFactory::getBySlugAndLanguage('links', 'en');
        $id = $page->getId();

        $controller = new PageController();
        $actual = $controller->_getParentPageId($id);

        $this->assertNull($actual->id);
    }

    public function testGetParentPageIdThrowsException(): void {
        $this->expectException(DatasetNotFoundException::class);
        $controller = new PageController();
        $controller->_getParentPageId(PHP_INT_MAX);
    }

    protected function createTestPages(): array {
        $pages = [];
        $slugs = ['unit-test', 'unit-test-2', 'unit-test-3'];

        foreach ($slugs as $slug) {
            $page = new Page();
            $page->title = 'Unit Test ' . time();
            $page->slug = $slug;
            $page->language = 'de';
            $page->content = 'Some Text';
            $page->comments_enabled = true;
            $page->author_id = 1;
            $page->group_id = 1;
            $page->save();
            $pages[] = $page;
        }

        $parentPage = ContentFactory::getBySlugAndLanguage('google', 'en');
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-4';
        $page->parent_id = $parentPage->getId();
        $page->language = 'en';
        $page->content = 'Some Text';
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $pages[] = $page;
        return $pages;
    }

    protected function createDeletedPage(): Page {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = 'Some Text';
        $page->comments_enabled = true;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();
        $page->delete();

        return $page;
    }

    protected function createTestDiff(): object {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->slug = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = 'Old Text 1';
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $manager = new \App\Models\Users\UserManager();

        $user = $manager->getAllUsers()[0];
        VCS::createRevision($page->getID(), 'New Text', $user->getId());
        $historyId = Database::getLastInsertID();

        $result = new stdClass();
        $result->content_id = $page->getID();
        $result->history_id = $historyId;

        return $result;
    }
}
