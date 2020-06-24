<?php

use UliCMS\Models\Content\Language;
use UliCMS\Exceptions\NotImplementedException;

class ModelTest extends \PHPUnit\Framework\TestCase {

    protected function tearDown(): void {
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

    public function testSaveThrowsException() {
        $this->expectException(NotImplementedException::class);
        $model = new Model();

        $model->save();
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

    public function testFillVarsThrowNotImplementedException() {
        $this->expectException(NotImplementedException::class);
        $model = new TestModel();
        $model->doFillVars();
    }

    public function testCheckValueTypeWithRequiredNotFilled() {
        $this->expectException("InvalidArgumentException");
        Model::checkValueType(null, "str", true);
    }

    public function testCheckValueTypeWithoutType() {
        $this->assertTrue(
                Model::checkValueType("foo", null, false)
        );
    }

    public function testCheckValueWithString() {
        $this->assertTrue(
                Model::checkValueType("ein-string", "string", true)
        );
    }

    public function testCheckValueWithInt() {
        $this->expectException("InvalidArgumentException");
        Model::checkValueType(123, "string", true);
    }

    public function testCheckValueTypeWithNull() {
        $this->assertTrue(
                Model::checkValueType(null, "str", false)
        );
    }

    public function testCheckValueTypeWithInvalidClass() {
        $this->expectException("InvalidArgumentException");
        Model::checkValueType(new Image_Page(), "Page", false);
    }

    public function testReloadReturnsTrue() {
        $language = new Language();
        $language->loadByLanguageCode("de");

        $language->setName("Germanisch");
        $this->assertEquals("Germanisch", $language->getName());

        $this->assertTrue($language->reload());
        $this->assertNotEquals("Germanisch", $language->getName());
    }

    public function testReloadReturnsFalse() {
        $language = new Language();
        $this->assertFalse($language->reload());
    }

}

class TestModel extends Model {

    public function doFillVars($result = null) {
        $this->fillVars($result);
    }

}
