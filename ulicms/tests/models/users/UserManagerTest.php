<?php

class UserManagerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $this->tearDown();
        for ($i = 1; $i < 5; $i ++) {
            $user = new User();
            $user->setUsername("locked_user_" . $i);
            $user->setFirstname("Max");
            $user->setLastname("Muster");
            $user->setGroupId(1);
            $user->setPassword("password123");
            $user->setLocked(1);
            $user->save();
        }
        for ($i = 1; $i <= 3; $i ++) {
            $user = new User();
            $user->setUsername("unlocked_user_" . $i);
            $user->setFirstname("Max");
            $user->setLastname("Muster");
            $user->setGroupId(1);
            $user->setPassword("password123");
            $user->setLocked(0);
            $user->save();
        }
    }

    public function tearDown() {
        for ($i = 1; $i < 5; $i ++) {
            $user = new User();
            $user->loadByUsername("locked_user_" . $i);
            $user->delete();
        }
        for ($i = 1; $i <= 3; $i ++) {
            $user = new User();
            $user->loadByUsername("unlocked_user_" . $i);
            $user->delete();
        }
    }

    public function testGetLocked() {
        $manager = new UserManager();
        $this->assertTrue(count($manager->getLockedUsers()) >= 3);

        $this->assertTrue(count($manager->getLockedUsers(false)) >= 4);
    }

    public function testGetByGroup() {
        $manager = new UserManager();
        $this->assertEquals(array(), $manager->getUsersByGroupId(666));

        $this->assertEquals(array(), $manager->getUsersByGroupId(null));
        $this->assertTrue(count($manager->getUsersByGroupId(1)) >= 7);
    }

}
