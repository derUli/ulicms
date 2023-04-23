<?php

use App\Exceptions\NotImplementedException;
use App\Models\Content\Language;

class ModelTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::pQuery('delete from {prefix}languages where language_code = ?', ['it'], true);
    }

    public function testIsPersistent(): void {
        $language = new Language();
        $language->setLanguageCode('it');
        $language->setName('Italiano');
        $this->assertFalse($language->isPersistent());

        $language->save();
        $this->assertTrue($language->isPersistent());

        $language->delete();
        $this->assertFalse($language->isPersistent());
    }

    public function testHasChanges(): void {
        $language = new Language();

        $this->assertFalse($language->hasChanges());
        $language->setLanguageCode('it');
        $language->setName('Italiano');

        $this->assertTrue($language->hasChanges());

        $language->save();

        $this->assertFalse($language->hasChanges());

        $language->setName('Venedig');
        $this->assertTrue($language->hasChanges());

        $language->save();
        $this->assertFalse($language->hasChanges());

        $language->delete();
        $this->assertFalse($language->hasChanges());
    }

    public function testLoadByIdThrowsException(): void {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->loadByID(123);
    }

    public function testSaveThrowsException(): void {
        $this->expectException(NotImplementedException::class);
        $model = new Model();

        $model->save();
    }

    public function testInsertThrowsNotImplementedException(): void {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->save();
    }

    public function testUpdateThrowsNotImplementedException(): void {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->setID(123);
        $model->save();
    }

    public function testDeleteThrowsNotImplementedException(): void {
        $this->expectException(NotImplementedException::class);
        $model = new Model();
        $model->delete();
    }

    public function testFillVarsThrowNotImplementedException(): void {
        $this->expectException(NotImplementedException::class);
        $model = new TestModel();
        $model->doFillVars();
    }

    public function testCheckValueTypeWithRequiredNotFilled(): void {
        $this->expectException('InvalidArgumentException');
        Model::checkValueType(null, 'str', true);
    }

    public function testCheckValueTypeWithoutType(): void {
        $this->assertTrue(
            Model::checkValueType('foo', null, false)
        );
    }

    public function testCheckValueWithString(): void {
        $this->assertTrue(
            Model::checkValueType('ein-string', 'string', true)
        );
    }

    public function testCheckValueWithInt(): void {
        $this->expectException('InvalidArgumentException');
        Model::checkValueType(123, 'string', true);
    }

    public function testCheckValueTypeWithNull(): void {
        $this->assertTrue(
            Model::checkValueType(null, 'str', false)
        );
    }

    public function testCheckValueTypeWithInvalidClass(): void {
        $this->expectException('InvalidArgumentException');
        Model::checkValueType(new Image_Page(), 'Page', false);
    }

    public function testReloadReturnsTrue(): void {
        $language = new Language();
        $language->loadByLanguageCode('de');

        $language->setName('Germanisch');
        $this->assertEquals('Germanisch', $language->getName());

        $this->assertTrue($language->reload());
        $this->assertNotEquals('Germanisch', $language->getName());
    }

    public function testReloadReturnsFalse(): void {
        $language = new Language();
        $this->assertFalse($language->reload());
    }
}

class TestModel extends Model {
    public function doFillVars($result = null): void {
        $this->fillVars($result);
    }
}
