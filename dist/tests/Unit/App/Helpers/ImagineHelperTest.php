<?php

use App\Helpers\ImagineHelper;
use Imagine\Image\AbstractImagine;

class ImagineHelperTest extends \PHPUnit\Framework\TestCase {
    public function testGetImage(): void {
        $image = ImagineHelper::getImagine();
        $this->assertInstanceOf(AbstractImagine::class, $image);
    }
}
