<?php

use UliCMS\Exceptions\NotImplementedException;

class PageControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_SESSION = [];
    }

    public function tearDown() {
        $_SESSION = [];
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

}
