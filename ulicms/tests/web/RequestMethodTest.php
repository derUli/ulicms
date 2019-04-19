<?php

class RequestMethodTest extends \PHPUnit\Framework\TestCase
{

    public function testPost()
    {
        $this->assertEquals("post", RequestMethod::POST);
    }

    public function testGet()
    {
        $this->assertEquals("get", RequestMethod::GET);
    }
}