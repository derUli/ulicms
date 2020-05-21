<?php

use UliCMS\Models\Content\Language;
use UliCMS\Exceptions\NotImplementedException;

class PageControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_SESSION = [];
    }

    public function tearDown() {
        $_SESSION = [];
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];
        $user->setSecondaryGroups([]);
        $user->save();
    }

    public function testGetPagesListViewNotSetReturnsDefault() {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals("default", $controller->_getPagesListView());
    }

    public function testGetPagesListReturnsDefault() {
        $_SESSION["pages_list_view"] = "default";

        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals("default", $controller->_getPagesListView());
    }

    public function testGetPagesListReturnsRecycleBin() {
        $_SESSION["pages_list_view"] = "recycle_bin";

        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals("recycle_bin", $controller->_getPagesListView());
    }

    public function testcheckIfSlugIsFreeReturnsTrue() {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertTrue(
                $controller->_checkIfSlugIsFree(uniqid(), "de", PHP_INT_MAX)
        );
    }

    public function testcheckIfSlugIsFreeWithEmptyReturnsTrue() {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertTrue(
                $controller->_checkIfSlugIsFree("", "de", PHP_INT_MAX)
        );
    }

    public function testcheckIfSlugIsFreeReturnsFalse() {
        $allSlugs = getAllSlugs("de");
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertFalse(
                $controller->_checkIfSlugIsFree($allSlugs[0], "de", PHP_INT_MAX)
        );
    }

    public function testGetBooleanSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getBooleanSelection();
        $this->assertCount(3, $items);

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetCategorySelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getCategorySelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertGreaterThanOrEqual(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetParentSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getCategorySelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertGreaterThanOrEqual(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetMenuSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getMenuSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetTypeSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getTypeSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetLanguageSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getLanguageSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetLanguageSelectionGroupAssigned() {
        $user = $this->getTestUser();
        $_SESSION["login_id"] = $user->getId();

        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->_getLanguageSelection();
        $this->assertCount(2, $items);
    }

    public function getTestUser() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $german = new Language();
        $german->loadByLanguageCode("de");

        $group = new Group();
        $group->setLanguages([$german]);
        $group->setName("test-group-" . uniqid());
        $group->save();

        $user->setSecondaryGroups([$group]);

        $user->save();

        return $user;
    }

    public function testGetParentIds() {
        $controller = ControllerRegistry::get(PageController::class);
        $parentIds = $controller->_getParentIds();

        $this->assertGreaterThanOrEqual(4, count($parentIds));

        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetParentIdsWithLanguageAndMenu() {
        $controller = ControllerRegistry::get(PageController::class);

        $parentIds = $controller->_getParentIds("en", "top");

        $this->assertGreaterThanOrEqual(2, count($parentIds));

        $this->assertLessThan(
                count($controller->_getParentIds()
                ), count($parentIds));

        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetParentIdsWithRestrictedGroups() {
        $controller = ControllerRegistry::get(PageController::class);
        $allIds = $parentIds = $controller->_getParentIds();

        $user = $this->getTestUser();
        $_SESSION["login_id"] = $user->getId();

        $controller = ControllerRegistry::get(PageController::class);

        $parentIds = $controller->_getParentIds();

        $this->assertGreaterThanOrEqual(2, count($parentIds));

        $this->assertLessThan(
                count($allIds
                ), count($parentIds));
        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetContentTypes() {
        $controller = new PageController();

        $actual = $controller->_getContentTypes();
        $expected = file_get_contents(
                Path::resolve("ULICMS_ROOT/tests/fixtures/getContentTypes.expected.json"));

        $this->assertEquals(normalizeLN($expected), normalizeLN($actual));
    }

    public function testToggleFilters() {
        $_SESSION["login_id"] = 666;
        $controller = new PageController();

        $this->assertTrue($controller->_toggleFilters());
        $this->assertFalse($controller->_toggleFilters());
    }

    public function testToggleShowPositions() {
        $_SESSION["login_id"] = 666;
        $controller = new PageController();

        $this->assertTrue($controller->_toggleShowPositions());
        $this->assertFalse($controller->_toggleShowPositions());
    }

    public function testPages() {
        $controller = new PageController();
        $controller->_pages();
        $this->assertEquals("default", $_SESSION["pages_list_view"]);
    }

    public function testRecycleBin() {
        $controller = new PageController();
        $controller->_recycleBin();
        $this->assertEquals("recycle_bin", $_SESSION["pages_list_view"]);
    }

    public function testGetCKEditorLinkList() {
        $controller = new PageController();
        $links = $controller->_getCKEditorLinkList();
        $this->assertGreaterThanOrEqual(1, count($links));

        foreach ($links as $link) {
            $this->assertCount(2, $link);
            $this->assertNotEmpty($link[0]);
            $this->assertStringContainsString(".html", $link[1]);
        }
    }

    public function testDiffContents() {
        throw new NotImplementedException();
    }

}
