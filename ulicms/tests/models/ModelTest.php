<?php

use UliCMS\Models\Content\Language;
use UliCMS\Exceptions\NotImplementedException;

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

    public function testLoadByIdThrowsException() {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->loadByID(123);
    }

    public function testInsertThrowsNotImplementedException() {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->save();
    }

    public function testUpdateThrowsNotImplementedException() {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->setID(123);
        $model->save();
    }

    public function testDeleteThrowsNotImplementedException() {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->delete();
    }

}
