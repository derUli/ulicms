<?php

use  App\Helpers\BooleanHelper;
use PHPUnit\Framework\TestCase;

class BooleanHelperTest extends TestCase
{
    public function testBool2YesNo(): void
    {
        $this->assertEquals(get_translation('yes'), BooleanHelper::bool2YesNo(true));
        $this->assertEquals(get_translation('no'), BooleanHelper::bool2YesNo(false));
    }

    public function testBool2YesNoWithCustomText(): void {
        $this->assertEquals('cool', BooleanHelper::bool2YesNo(true, 'cool', 'doof'));
        $this->assertEquals('doof', BooleanHelper::bool2YesNo(false, 'cool', 'doof'));
    }
}
