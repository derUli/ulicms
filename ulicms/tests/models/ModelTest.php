<?php

use UliCMS\Models\Content\Language;

class ModelTest extends \PHPUnit\Framework\TestCase {

    public function testIsPersistent() {
        $language = new Language();
        $language->setLanguageCode("it");
        $language->setName("Italiano");
        $this->assertFalse($language->isPersistent());

        $language->save();
        $this->assertTrue($language->isPersistent());

        $language->delete();
        $this->assertFalse($language->isPersistent());
    }

}
