<?php

use App\Constants\RequestMethod;

class RequestMethodTest extends \PHPUnit\Framework\TestCase {
    public function testPost(): void {
        $this->assertEquals('post', RequestMethod::POST);
    }

    public function testGet(): void {
        $this->assertEquals('get', RequestMethod::GET);
    }
}
