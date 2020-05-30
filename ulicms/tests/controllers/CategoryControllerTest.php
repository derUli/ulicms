<?php

use UliCMS\Models\Content\Category;

class CategoryControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        LoggerRegistry::register(
                "audit_log",
                new Logger(Path::resolve("ULICMS_LOG/audit_log"))
        );
    }

    public function tearDown() {
        LoggerRegistry::unregister("audit_log");
        Database::deleteFrom("categories", "name like 'Unit Test%'");
    }

    public function testCreateCategory() {
        $name = "Unit Test " . time();
        $description = "Description " . time();
        $controller = new CategoryController();
        $id = $controller->_createPost($name, $description);

        $this->assertGreaterThan(1, $id);

        $banner = new Category($id);
        $this->assertEquals($name, $banner->getName());
        $this->assertEquals($description, $banner->getDescription());
    }

}
