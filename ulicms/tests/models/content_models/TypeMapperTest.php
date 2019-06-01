<?php

use UliCMS\Models\Content\TypeMapper;

class TypeMapperTest extends \PHPUnit\Framework\TestCase {

    public function testGetMappings() {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $this->assertTrue(class_exists($modelClass));
            $model = new $modelClass();
            $this->assertEquals($type, $model->type);
        }
    }

    public function testGetModelReturnsModel() {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $model = TypeMapper::getModel($type);
            $this->assertInstanceOf($modelClass, $model);
            $this->assertEquals($type, $model->type);
        }
    }

    public function testGetModelReturnsNull() {
        $this->assertNull(TypeMapper::getModel("magic_page"));
    }

}
