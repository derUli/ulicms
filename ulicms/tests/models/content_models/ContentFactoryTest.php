<?php

class ContentFactoryTest extends \PHPUnit\Framework\TestCase {

    public function testCreateModels() {
        $types = TypeMapper::getMappings();
        $this->assertGreaterThanOrEqual(11, count($types));

        foreach ($types as $type => $modelClass) {
            $content = ContentFactory::getAllByType($types);
            foreach ($content as $page) {
                $this->assertInstanceOf($modelClass, $page);
            }
        }
    }

}
