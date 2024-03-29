<?php

use App\Models\Content\TypeMapper;

class TypeMapperTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        TypeMapper::loadMapping();
    }

    public function testGetMappings(): void {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $this->assertTrue(class_exists($modelClass));
            $model = new $modelClass();
            $this->assertEquals($type, $model->type);
        }
    }

    public function testGetModelReturnsModel(): void {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $model = TypeMapper::getModel($type);
            $this->assertInstanceOf($modelClass, $model);
            $this->assertEquals($type, $model->type);
        }
    }

    public function testGetModelReturnsNull(): void {
        $this->assertNull(TypeMapper::getModel('magic_page'));
    }
}
