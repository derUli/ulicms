<?php

use Imagine\Image\AbstractImagine;

class ImagineHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testGetImage()
    {
        $image = ImagineHelper::getImagine();
        $this->assertInstanceOf(AbstractImagine::class, $image);
    }

    public function testGetLibraryName()
    {
        $this->assertTrue(
            in_array(
                ImagineHelper::getLibraryName(),
                ['gd', 'imagick', 'gmagick']
            )
        );
    }
}
