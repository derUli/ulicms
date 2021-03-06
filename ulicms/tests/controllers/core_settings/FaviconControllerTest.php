<?php

class FaviconControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $controller = new FaviconController();
        $file1 = $controller->_getDestination1();
        $file2 = $controller->_getDestination2();

        if (file_exists($file1)) {
            rename($file1, "{$file1}.bak");
        }

        if (file_exists($file2)) {
            rename($file2, "{$file2}.bak");
        }
    }

    protected function tearDown(): void
    {
        $controller = new FaviconController();
        $file1 = $controller->_getDestination1() . ".bak";
        $file2 = $controller->_getDestination2() . ".bak";

        if (file_exists($file1)) {
            rename($file1, $controller->_getDestination1());
        }

        if (file_exists($file2)) {
            rename($file2, $controller->_getDestination2());
        }
    }

    public function testGetSizes()
    {
        $controller = new FaviconController();
        $sizes = $controller->_getSizes();
        $this->assertCount(2, $sizes);
        $this->assertEquals([32, 32], $sizes[0]);
        $this->assertEquals([64, 64], $sizes[1]);
    }

    public function testGetSizesWithHighResolution()
    {
        $controller = new FaviconController();
        $sizes = $controller->_getSizes(true);
        $this->assertCount(3, $sizes);
        $this->assertEquals([32, 32], $sizes[0]);
        $this->assertEquals([64, 64], $sizes[1]);
        $this->assertEquals([128, 128], $sizes[2]);
    }

    public function testGetDestination1()
    {
        $controller = new FaviconController();

        $this->assertStringEndsWith(
            "/content/images/favicon.ico",
            $controller->_getDestination1()
        );
    }

    public function testGetDestination2()
    {
        $controller = new FaviconController();

        $this->assertStringEndsWith(
            "/favicon.ico",
            $controller->_getDestination2()
        );
        $this->assertStringEndsNotWith(
            "/content/images/favicon.ico",
            $controller->_getDestination2()
        );
    }

    public function testPutAndDeleteFiles()
    {
        $source = Path::resolve("ULICMS_ROOT/tests/fixtures/cat.jpg");
        $controller = new FaviconController();
        $this->assertTrue(
            $controller->_placeFiles($source, $controller->_getSizes())
        );

        $this->assertTrue($controller->_hasFavicon());
        
        $this->assertEquals(
            "7005a4a7c5933da712ff8766bfd21d33",
            md5_file($controller->_getDestination1())
        );
        $this->assertEquals(
            "7005a4a7c5933da712ff8766bfd21d33",
            md5_file($controller->_getDestination2())
        );

        $this->assertTrue($controller->_deleteFavicon());
        $this->assertFileDoesNotExist($controller->_getDestination1());
        $this->assertFileDoesNotExist($controller->_getDestination2());
    }

    public function testPutAndDeleteFilesHQ()
    {
        $source = Path::resolve("ULICMS_ROOT/tests/fixtures/cat.jpg");
        $controller = new FaviconController();
        $this->assertTrue(
            $controller->_placeFiles($source, $controller->_getSizes(true))
        );

        $this->assertEquals(
            "d4150ed4acdf20fe4a9c4f31ace5e262",
            md5_file($controller->_getDestination1())
        );
        $this->assertEquals(
            "d4150ed4acdf20fe4a9c4f31ace5e262",
            md5_file($controller->_getDestination2())
        );

        $this->assertTrue($controller->_deleteFavicon());
        $this->assertFalse($controller->_hasFavicon());
        $this->assertFileDoesNotExist($controller->_getDestination1());
        $this->assertFileDoesNotExist($controller->_getDestination2());
    }
}
