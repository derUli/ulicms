<?php

use Spatie\Snapshots\MatchesSnapshots;

class FaviconControllerTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    protected function setUp(): void {
        $controller = new FaviconController();
        $file1 = $controller->_getDestination1();
        $file2 = $controller->_getDestination2();

        if (is_file($file1)) {
            rename($file1, "{$file1}.bak");
        }

        if (is_file($file2)) {
            rename($file2, "{$file2}.bak");
        }
    }

    protected function tearDown(): void {
        $controller = new FaviconController();
        $file1 = $controller->_getDestination1() . '.bak';
        $file2 = $controller->_getDestination2() . '.bak';

        if (is_file($file1)) {
            rename($file1, $controller->_getDestination1());
        }

        if (is_file($file2)) {
            rename($file2, $controller->_getDestination2());
        }
    }

    public function testGetSizes(): void {
        $controller = new FaviconController();
        $sizes = $controller->_getSizes();
        $this->assertCount(2, $sizes);
        $this->assertEquals([32, 32], $sizes[0]);
        $this->assertEquals([64, 64], $sizes[1]);
    }

    public function testGetSizesWithHighResolution(): void {
        $controller = new FaviconController();
        $sizes = $controller->_getSizes(true);
        $this->assertCount(3, $sizes);
        $this->assertEquals([32, 32], $sizes[0]);
        $this->assertEquals([64, 64], $sizes[1]);
        $this->assertEquals([128, 128], $sizes[2]);
    }

    public function testGetDestination1(): void {
        $controller = new FaviconController();

        $this->assertStringEndsWith(
            '/content/images/favicon.ico',
            $controller->_getDestination1()
        );
    }

    public function testGetDestination2(): void {
        $controller = new FaviconController();

        $this->assertStringEndsWith(
            '/favicon.ico',
            $controller->_getDestination2()
        );
        $this->assertStringEndsNotWith(
            '/content/images/favicon.ico',
            $controller->_getDestination2()
        );
    }

    public function testPutAndDeleteFiles(): void {
        $source = \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/cat.jpg');
        $controller = new FaviconController();
        $this->assertTrue(
            $controller->_placeFiles($source, $controller->_getSizes())
        );

        $this->assertTrue($controller->_hasFavicon());

        $this->assertMatchesFileHashSnapshot($controller->_getDestination1());
        $this->assertMatchesFileHashSnapshot($controller->_getDestination2());

        $this->assertTrue($controller->_deleteFavicon());
        $this->assertFileDoesNotExist($controller->_getDestination1());
        $this->assertFileDoesNotExist($controller->_getDestination2());
    }

    public function testPutAndDeleteFilesHQ(): void {
        $source = \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/cat.jpg');
        $controller = new FaviconController();
        $this->assertTrue(
            $controller->_placeFiles($source, $controller->_getSizes(true))
        );

        $this->assertMatchesFileHashSnapshot($controller->_getDestination1());
        $this->assertMatchesFileHashSnapshot($controller->_getDestination2());

        $this->assertTrue($controller->_deleteFavicon());
        $this->assertFalse($controller->_hasFavicon());
        $this->assertFileDoesNotExist($controller->_getDestination1());
        $this->assertFileDoesNotExist($controller->_getDestination2());
    }
}
