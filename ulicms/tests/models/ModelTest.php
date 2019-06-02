<?php

use UliCMS\Models\Content\Language;

class ModelTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::pQuery("delete from {prefix}languages where language_code = ?", array("it"), true);
    }

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

    public function testHasChanges() {
        $language = new Language();

        $this->assertFalse($language->hasChanges());
        $language->setLanguageCode("it");
        $language->setName("Italiano");

        $this->assertTrue($language->hasChanges());

        $language->save();

        $this->assertFalse($language->hasChanges());

        $language->setName("Venedig");
        $this->assertTrue($language->hasChanges());

        $language->save();
        $this->assertFalse($language->hasChanges());

        $language->delete();
        $this->assertFalse($language->hasChanges());
    }

}
