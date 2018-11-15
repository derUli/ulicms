<?php

class ModelTest extends \PHPUnit\Framework\TestCase
{

    public function tearDown()
    {
        Model::setModel(null);
        BackendHelper::setAction("home");
    }

    public function testModelIsNullByDefault()
    {
        $this->assertNull(Model::getModel());
    }

    public function testSetAndGetModel()
    {
        Model::setModel(new Page());
        $this->assertInstanceOf(Page::class, Model::getModel());
        
        Model::setModel(null);
    }

    public function testActionResult()
    {
        ActionResult("foo", new User());
        
        $this->assertEquals("foo", BackendHelper::getAction());
        $this->assertInstanceOf(User::class, Model::getModel());
    }
}