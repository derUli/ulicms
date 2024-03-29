<?php

class PkgInfoControllerTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        \App\Storages\Vars::delete('allModules');

        $source = \App\Utils\Path::resolve(
            'ULICMS_ROOT/tests/fixtures/packages/hello_world-1.0.sin'
        );

        $destination = \App\Utils\Path::resolve(
            'ULICMS_TMP/hello_world-10.sin'
        );

        copy($source, $destination);
    }

    protected function tearDown(): void {
        $destination = \App\Utils\Path::resolve(
            'ULICMS_TMP/hello_world-10.sin'
        );
        if (is_file($destination)) {
            unlink($destination);
        }

        uninstall_module('hello_world');
    }

    public function testInstallPostReturnsTrue(): void {
        $controller = new PkgInfoController();
        $success = $controller->_installPost('hello_world-10.sin');
        $this->assertTrue($success);

        \App\Storages\Vars::delete('allModules');

        $this->assertContains('hello_world', getAllModules());
    }

    public function testInstallPostReturnsFalse(): void {
        $controller = new PkgInfoController();
        $success = $controller->_installPost('gibtsnicht');
        $this->assertFalse($success);
    }
}
