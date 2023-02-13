<?php

use function App\Utils\Session\sessionName;

class SessionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSessionName()
    {
        $this->assertStringContainsString("SESS", sessionName());
    }

    public function testSetSessionName()
    {
        $this->assertStringEndsWith(sessionName(), sessionName("foo"));
    }
}
