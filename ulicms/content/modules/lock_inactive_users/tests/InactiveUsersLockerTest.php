<?php
use UliCMS\Exceptions\NotImplementedException;

class InactiveUsersLockerTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp()
    {
        // User without last login, must not be locked
        $user = new User();
        $user->setUsername("testuser1");
        $user->setLastname("Doe");
        $user->setFirstname("John");
        $user->setPassword("foobar");
        $user->save();
        
        // User without last login, must not be locked
        $user = new User();
        $user->setUsername("testuser2");
        $user->setLastname("Doe");
        $user->setFirstname("John");
        $user->setPassword("foobar");
        // 2 days ago
        $user->setLastLogin(time() - (2 * 24 * 60 * 60));
        $user->save();
        
        // User without last login, must not be locked
        $user = new User();
        $user->setUsername("testuser3");
        $user->setLastname("Doe");
        $user->setFirstname("John");
        $user->setPassword("foobar");
        // 31 days ago
        $user->setLastLogin(time() - (32 * 24 * 60 * 60));
        $user->save();
        
        // User without last login, must not be locked
        $user = new User();
        $user->setUsername("testuser4");
        $user->setLastname("Doe");
        $user->setFirstname("John");
        $user->setPassword("foobar");
        // 31 days ago
        $user->setLastLogin(time() - (40 * 24 * 60 * 60));
        $user->save();
    }

    protected function tearDown()
    {
        // delete test users
        Database::query("delete from `{prefix}users` where username like 'testuser%'", true);
    }

    // constructor with days
    // check that days were set
    public function testGetDays()
    {
        $locker = new InactiveUsersLocker(123);
        $this->assertEquals(123, $locker->getDays());
    }

    // two users should get locked the other two not
    public function testLockInactiveUsers()
    {
        $this->assertTrue(user_exists("testuser1"));
        $this->assertTrue(user_exists("testuser2"));
        $this->assertTrue(user_exists("testuser3"));
        $this->assertTrue(user_exists("testuser4"));
        
        $locker = new InactiveUsersLocker(30);
        $this->assertEquals(2, $locker->lockInactiveUsers());
        
        $testUser1 = new User();
        $testUser1->loadByUsername("testuser1");
        
        $testUser2 = new User();
        $testUser2->loadByUsername("testuser2");
        
        $testUser3 = new User();
        $testUser3->loadByUsername("testuser3");
        
        $testUser4 = new User();
        $testUser4->loadByUsername("testuser4");
        
        $this->assertFalse($testUser1->getLocked());
        $this->assertFalse($testUser2->getLocked());
        $this->assertTrue($testUser3->getLocked());
        $this->assertTrue($testUser4->getLocked());
    }
}

