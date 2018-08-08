<?php
include dirname(__file__) . "/ModelBinderExample.php";

class ModelBinderTest extends PHPUnit_Framework_TestCase
{

    public function testModelBind()
    {
        $model = new ModelBinderExample();
        $model->fillVars($query);
        $this->assertEquals(123, $model->getField1());
        $this->assertEquals("This is string", $model->getField2());
        try {
            $model->fillInvalid1();
            $this->fail("Invalid binding did not throw exception");
        } catch (InvalidArgumentException $e) {
            $this->assertNotNull("invalid binding failed");
        }
        $this->assertNull($model->getMyField());
        
        $model->fillInvalid2();
        $this->assertNull($model->getNotMapped());
    }
}