<?php

use function App\Utils\Session\sessionName;

class SessionTest extends \PHPUnit\Framework\TestCase {
    public function testGetSessionName(): void {
        $this->assertStringContainsString('SESS', sessionName());
    }

    public function testSetSessionName(): void {
        $this->assertStringEndsWith(sessionName(), sessionName('foo'));
    }
}
