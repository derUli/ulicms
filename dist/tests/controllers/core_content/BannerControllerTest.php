<?php

use App\Exceptions\DatasetNotFoundException;
use App\Models\Content\Advertisement\Banner;

class BannerControllerTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $_POST = [];
    }

    protected function tearDown(): void {
        $_POST = [];

        Database::pQuery(
            'DELETE FROM `{prefix}banner` where html in (?) or '
            . 'name = ?',
            [
                'Werbung nervt',
                'Nervige Werbung'
            ],
            true
        );
    }

    public function testCreateReturnsModel() {
        $this->setPostVars();

        $controller = new BannerController();
        $banner = $controller->_createPost();

        $this->assertInstanceOf(Banner::class, $banner);
        $this->assertGreaterThanOrEqual(1, $banner->getId());
    }

    public function testUpdateReturnsModel() {
        $this->setPostVars();
        $controller = new BannerController();
        $id = $controller->_createPost()->getId();
        $_POST['id'] = $id;

        $_POST['link_url'] = 'https://google.com';
        $banner = $controller->_updatePost();

        $this->assertInstanceOf(Banner::class, $banner);
        $this->assertGreaterThanOrEqual(1, $banner->getId());
        $this->assertEquals('https://google.com', $banner->getLinkUrl());
    }

    public function testDeletePostReturnsTrue() {
        $banner = new Banner();
        $banner->setType('html');
        $banner->setHtml('Werbung nervt');
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

    protected function setPostVars() {
        $_POST['banner_name'] = 'Nervige Werbung';
        $_POST['image_url'] = '';
        $_POST['link_url'] = '';
        $_POST['category_id'] = 1;
        $_POST['type'] = 'html';
        $_POST['language'] = 'de';
        $_POST['enabled'] = 1;
        $_POST['html'] = 'Foo Bar';
        $_POST['date_from'] = '';
        $_POST['date_to'] = '';
    }
}
