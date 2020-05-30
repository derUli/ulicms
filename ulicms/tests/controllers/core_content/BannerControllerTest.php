<?php
use UliCMS\Models\Content\Advertisement\Banner;
use UliCMS\Exceptions\DatasetNotFoundException;

class BannerControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        LoggerRegistry::register(
                "audit_log",
                new Logger(Path::resolve("ULICMS_LOG/audit_log"))
        );
    }

    public function tearDown() {
        LoggerRegistry::unregister("audit_log");
        Database::pQuery(
                "DELETE FROM `{prefix}banner` where html in (?)",
                [
                    "Werbung nervt"
                ]
                , true);
    }

    public function testDeletePostReturnsTrue() {
        $banner = new Banner();
        $banner->setType("html");
        $banner->setHtml("Werbung nervt");
        $banner->save();

        $controller = new BannerController();
        $success = $controller->_deletePost($banner->getId());
        $this->assertTrue($success);
        
        $this->expectException(DatasetNotFoundException::class);
        $banner->reload();
        $this->assertFalse($banner->isPersistent());
    }

    public function testDeletePostReturnsFalse() {
        $controller = new BannerController();
        $success = $controller->_deletePost(PHP_INT_MAX);
        $this->assertFalse($success);
    }

}
