<?php

use Imagine\Image\AbstractImagine;
class ImagineHelperTest extends \PHPUnit\Framework\TestCase {

    public function testGetImage() {
        $image = ImagineHelper::getImagine();
        $this->assertInstanceOf(AbstractImagine::class, $image);
    }
    
    public function testIsSVGSupportAvailable(){
        $this->assertIsBool(ImagineHelper::isSVGSupportAvailable());
    }

}
