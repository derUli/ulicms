<?php

use App\Security\Permissions\ACL;

class ACLTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $_SESSION = [];
    }

    protected function tearDown(): void {
        $_SESSION = [];
    }

    public function testGetDefaultACL(): void {
        $actual = ACL::getDefaultACL(false);

        $this->assertIsArray($actual);

        foreach($actual as $key => $value) {
            $this->assertIsString($key);
            $this->assertFalse($value);
        }
    }

    public function testGetDefaultACLAdmin(): void {
        $actual = ACL::getDefaultACL(true);

        $this->assertIsArray($actual);

        foreach($actual as $key => $value) {
            $this->assertIsString($key);
            $this->assertTrue($value);
        }
    }
}
