<?php

use App\Helpers\ImageScaleHelper;

class ImageScaleHelperTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        if (is_file($this->getProcessedPath())) {
            unlink($this->getProcessedPath());
        }
    }

    public function testGetMaxImageDimensions(): void {
        $this->assertEquals(
            [2500, 1667],
            ImageScaleHelper::getMaxImageDimensions()
        );
    }

    public function testScaleDown(): void {
        ImageScaleHelper::scaleDown(
            $this->getFixturePath(),
            $this->getProcessedPath()
        );

        // check file size
        $this->assertLessThanOrEqual(
            400 * 1000,
            filesize($this->getProcessedPath())
        );

        // Check image size
        $imagine = new Imagine\Gd\Imagine();
        $image = $imagine->open($this->getProcessedPath());
        $size = $image->getSize();
        $this->assertEquals(1667, $size->getWidth());
        $this->assertEquals(1667, $size->getHeight());
    }

    public function testGetSrcSetDimensions(): void {
        $dimensions = ImageScaleHelper::getSrcSetDimensions();

        $this->assertEquals([2500, 1667], $dimensions['default']);
        $this->assertEquals([2500, 1667], $dimensions['2500']);
        $this->assertEquals([1250, 833], $dimensions['1250']);
        $this->assertEquals([625, 416], $dimensions['625']);
        $this->assertEquals([312, 208], $dimensions['312']);
    }

    protected function getFixturePath(): string {
        return \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/huge-image.jpg');
    }

    protected function getProcessedPath(): string {
        return \App\Utils\Path::resolve('ULICMS_TMP/scaled-image.jpg');
    }
}
