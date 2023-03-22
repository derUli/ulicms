<?php

use Imagine\Image\AbstractImagine;
use App\Helpers\ImagineHelper;

class ImagineHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testGetImage()
    {
        $image = ImagineHelper::getImagine();
        $this->assertInstanceOf(AbstractImagine::class, $image);
    }
}
