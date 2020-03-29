<?php

use UliCMS\Models\Content\Language;

class PageControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_SESSION = [];
    }

    public function tearDown() {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];
        $user->setSecondaryGroups([]);
        $user->save();
    }

    public function testGetPagesListViewNotSetReturnsDefault() {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals("default", $controller->getPagesListView());
    }

    public function testGetPagesListReturnsDefault() {
        $_SESSION["pages_list_view"] = "default";

        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals("default", $controller->getPagesListView());
    }

    public function testGetPagesListReturnsRecycleBin() {
        $_SESSION["pages_list_view"] = "recycle_bin";

        $controller = ControllerRegistry::get(PageController::class);
        $this->assertEquals("recycle_bin", $controller->getPagesListView());
    }

    public function testCheckIfSlugIsFreeReturnsTrue() {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertTrue(
                $controller->checkIfSlugIsFree(uniqid(), "de", PHP_INT_MAX)
        );
    }

    public function testCheckIfSlugIsFreeWithEmptyReturnsTrue() {
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertTrue(
                $controller->checkIfSlugIsFree("", "de", PHP_INT_MAX)
        );
    }

    public function testCheckIfSlugIsFreeReturnsFalse() {
        $allSlugs = getAllSlugs("de");
        $controller = ControllerRegistry::get(PageController::class);
        $this->assertFalse(
                $controller->checkIfSlugIsFree($allSlugs[0], "de", PHP_INT_MAX)
        );
    }

    public function testGetBooleanSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->getBooleanSelection();
        $this->assertCount(3, $items);

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetCategorySelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->getCategorySelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertGreaterThanOrEqual(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetParentSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->getCategorySelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertGreaterThanOrEqual(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetMenuSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->getMenuSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetTypeSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->getTypeSelection();
        $this->assertGreaterThanOrEqual(2, count($items));

        $this->assertNull($items[0]->getValue());
        $this->assertNotEmpty(1, $items[1]->getValue());

        foreach ($items as $item) {
            $this->assertNotEmpty($item->getText());
        }
    }

    public function testGetLanguageSelection() {
        $controller = ControllerRegistry::get(PageController::class);

        $items = $controller->getLanguageSelection();
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

        $items = $controller->getLanguageSelection();
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

        $parentIds = $controller->getParentIds();

        $this->assertGreaterThanOrEqual(4, count($parentIds));

        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetParentIdsWithLanguageAndMenu() {
        $controller = ControllerRegistry::get(PageController::class);

        $parentIds = $controller->getParentIds("en", "top");

        $this->assertGreaterThanOrEqual(2, count($parentIds));

        $this->assertLessThan(
                count($controller->getParentIds()
                ), count($parentIds));

        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

    public function testGetparentIdsWithRestrictedGroups() {
        $controller = ControllerRegistry::get(PageController::class);
        $allIds = $parentIds = $controller->getParentIds();

        $user = $this->getTestUser();
        $_SESSION["login_id"] = $user->getId();

        $controller = ControllerRegistry::get(PageController::class);

        $parentIds = $controller->getParentIds();

        $this->assertGreaterThanOrEqual(2, count($parentIds));

        $this->assertLessThan(
                count($allIds
                ), count($parentIds));
        foreach ($parentIds as $id) {
            $this->assertGreaterThanOrEqual(1, $id);
        }
    }

}
