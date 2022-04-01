<?php

use UliCMS\Utils\Session;

class SessionTest extends \PHPUnit\Framework\TestCase {

    public function testGetSessionName() {
        $this->assertStringContainsString("SESS", Session::sessionName());
    }

    public function testSetSessionName() {
        $this->assertStringEndsWith(Session::sessionName(), Session::sessionName("foo"));
    }

}
