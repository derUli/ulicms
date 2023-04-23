<?php

use App\Exceptions\NotImplementedException;
use App\Models\Content\CustomFields\CustomField;

class CustomFieldTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        include_once getLanguageFilePath('en');
    }

    public function testRender(): void {
        $this->expectException(NotImplementedException::class);
        $customField = new CustomField();
        $customField->render('foobar');
    }
}
