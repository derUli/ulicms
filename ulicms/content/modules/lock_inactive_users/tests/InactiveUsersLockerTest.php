<?php
use UliCMS\Exceptions\NotImplementedException;

class InactiveUsersLockerTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp()
    {
        // TODO: prepare test users
        // some must be inactive and some not
    }

    protected function tearDown()
    {
        // Todo: Delete test users
    }

    public function testGetDays()
    {
        $locker = new InactiveUsersLocker(123);
        $this->assertEquals(123, $locker->getDays());
    }

    public function testLockInactiveUsers()
    {
        throw new NotImplementedException();
    }
}

