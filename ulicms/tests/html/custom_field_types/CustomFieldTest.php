<?php

use UliCMS\Exceptions\NotImplementedException;

class CustomFieldTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        include_once getLanguageFilePath("en");
    }

    public function testRender() {
        $this->expectException(NotImplementedException::class);
        $customField = new CustomField();
        $customField->render("foobar");
    }

}
