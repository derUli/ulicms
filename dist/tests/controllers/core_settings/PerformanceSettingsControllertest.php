<?php

use App\Utils\File;

class PerformanceSettingsControllerTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        $_POST = [];
        Settings::delete('cache_disabled');
        Settings::delete('cache_period');

        Settings::set('lazy_loading_img', 1);
        Settings::set('lazy_loading_iframe', 1);
    }

    public function testSubmitEnabled(): void {
        $_POST['cache_enabled'] = '1';
        $_POST['cache_period'] = '4';
        $_POST['lazy_loading'] = ['img'];

        $controller = new PerformanceSettingsController();
        $controller->_savePost();

        $this->assertNull(Settings::get('cache_disabled'));
        $this->assertEquals('240', Settings::get('cache_period'));
        $this->assertEquals('1', Settings::get('lazy_loading_img'));
        $this->assertEquals('0', Settings::get('lazy_loading_iframe'));
    }

    public function testSubmitDisabled(): void {
        $_POST['cache_period'] = '2';
        $_POST['lazy_loading'] = ['img'];

        $controller = new PerformanceSettingsController();
        $controller->_savePost();

        $this->assertNotNull(Settings::get('cache_disabled'));
        $this->assertEquals('120', Settings::get('cache_period'));
        $this->assertEquals('1', Settings::get('lazy_loading_img'));
        $this->assertEquals('0', Settings::get('lazy_loading_iframe'));
    }

    public function testClearCache() {
        $controller = new PerformanceSettingsController();
        $controller->_clearCache();

        $files = File::findAllFiles(Path::resolve('ULICMS_CACHE'));

        $this->assertCount(0, $files);
    }
}
