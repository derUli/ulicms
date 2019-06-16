<?php

class TypeMapperTest extends \PHPUnit\Framework\TestCase {

    public function testCreateModels() {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $this->assertTrue(class_exists($modelClass));
            $model = new $modelClass();
            $this->assertEquals($type, $model->type);
        }
    }

}
