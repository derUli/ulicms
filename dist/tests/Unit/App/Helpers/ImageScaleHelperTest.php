<?php

use App\Helpers\ImageScaleHelper;
use App\Utils\Path;

class ImageScaleHelperTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        if (is_file($this->getProcessedPath())) {
            unlink($this->getProcessedPath());
        }
    }

    public function testGetMaxImageDimensions(): void {
        $this->assertEquals(
            [1920, 1080],
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
        $this->assertEquals(1080, $size->getWidth());
        $this->assertEquals(1080, $size->getHeight());
    }

    public function testGetSrcSetDimensions(): void {
        $dimensions = ImageScaleHelper::getSrcSetDimensions();

        $this->assertEquals([1920, 1080], $dimensions['default']);
        $this->assertEquals([1920, 1080], $dimensions['1920']);
        $this->assertEquals([960, 540], $dimensions['960']);
        $this->assertEquals([480, 270], $dimensions['480']);
    }

    public function testGetQualitySettingsJpg(): void {
        $this->assertEquals(
            80,
            ImageScaleHelper::getQualitySettings(
                Path::resolve('ULICMS_ROOT/tests/fixtures/cat.jpg')
            )['jpeg_quality']
        );
    }

    public function testGetQualitySettingsPng(): void {
        $this->assertEquals(
            9,
            ImageScaleHelper::getQualitySettings(
                Path::resolve('ULICMS_ROOT/admin/gfx/no_avatar.png')
            )['png_compression_level']
        );
    }

    public function testGetQualitySettingsWebp(): void {
        $this->assertEquals(
            80,
            ImageScaleHelper::getQualitySettings(
                Path::resolve('ULICMS_ROOT/tests/fixtures/test.webp')
            )['webp_quality']
        );
    }

    public function testGetQualitySettingsNoImage(): void {
        $this->assertEquals(
            [],
            ImageScaleHelper::getQualitySettings(
                Path::resolve('ULICMS_ROOT/index.php')
            )
        );
    }

    protected function getFixturePath(): string {
        return \App\Utils\Path::resolve('ULICMS_ROOT/tests/fixtures/huge-image.jpg');
    }

    protected function getProcessedPath(): string {
        return \App\Utils\Path::resolve('ULICMS_TMP/scaled-image.jpg');
    }
}
