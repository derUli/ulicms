<?php
use UliCMS\Security\PermissionChecker;

class PermissionCheckerTest extends \PHPUnit\Framework\TestCase
{

    public function testConstructorWithUserId()
    {
        $checker = new PermissionChecker(123);
        $this->assertEquals(123, $checker->getUserId());
    }

    public function testSetUserId()
    {
        $checker = new PermissionChecker();
        $this->assertNull($checker->getUserId());
        $checker->setUserId(666);
        $this->assertEquals(666, $checker->getUserId());
        $checker->setUserId(null);
        $this->assertNull($checker->getUserId());
    }
}