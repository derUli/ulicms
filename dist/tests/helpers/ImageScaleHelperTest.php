<?php

use App\Helpers\ImageScaleHelper;

class ImageScaleHelperTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        if (is_file($this->getProcessedPath())) {
            unlink($this->getProcessedPath());
        }
    }

    protected function getFixturePath(): string
    {
        return Path::resolve("ULICMS_ROOT/tests/fixtures/huge-image.jpg");
    }

    protected function getProcessedPath(): string
    {
        return Path::resolve("ULICMS_TMP/scaled-image.jpg");
    }

    public function testGetMaxImageDimensions()
    {
        $this->assertEquals(
            [2500, 1667],
            ImageScaleHelper::getMaxImageDimensions()
        );
    }

    public function testScaleDown()
    {
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
}
