<?php

class UsersApiTest extends \PHPUnit\Framework\TestCase
{

    private $testUser;

    public function setUp()
    {
        @session_start();
        unset($_SESSION["login_id"]);
        unset($_SESSION["logged_in"]);
        
        $user = new User();
        $user->setUsername("testuser1");
        $user->setLastname("Muster");
        $user->setFirstname("Max");
        $user->setPassword("topsecret");
        $user->save();
        $this->testUser = $user;
        
        $user = new User();
        $user->setUsername("testuser3");
        $user->setLastname("Muster");
        $user->setFirstname("Max");
        $user->setPassword("oldpassword");
        $user->save();
    }

    public function tearDown()
    {
        unset($_SESSION["login_id"]);
        unset($_SESSION["logged_in"]);
        
        $this->testUser->delete();
        
        $user = new User();
        $user->loadByUsername("testuser2");
        $user->delete();
        
        $user = new User();
        $user->loadByUsername("testuser3");
        $user->delete();
    }

    public function testGetUserIdUserIsLoggedIn()
    {
        $_SESSION["login_id"] = 123;
        $this->assertEquals(123, get_user_id());
        unset($_SESSION["login_id"]);
    }

    public function testGetUserIdUserIsNotLoggedIn()
    {
        $this->assertEquals(0, get_user_id());
    }

    public function testUserExistsTrue()
    {
        $this->assertTrue(user_exists("testuser1"));
    }

    public function testUserExistsFalse()
    {
        $this->assertFalse(user_exists("slenderman"));
    }

    public function testIsLoggedInTrue()
    {
        $_SESSION["logged_in"] = true;
        $this->assertTrue(is_logged_in());
    }

    public function testIsLoggedInFalse()
    {
        unset($_SESSION["logged_in"]);
        $this->assertFalse(is_logged_in());
    }

    public function testLoggedInTrue()
    {
        $_SESSION["logged_in"] = true;
        $this->assertTrue(logged_in());
    }

    public function testLoggedInFalse()
    {
        unset($_SESSION["logged_in"]);
        $this->assertFalse(logged_in());
    }

    public function testValidateLoginTrue()
    {
        $this->assertTrue(is_array(validate_login("testuser1", "topsecret")));
    }

    public function testValidateLoginFalse()
    {
        $this->assertFalse(validate_login("testuser1", "dasfalschepassword"));
    }

    public function testGetUsersOnlineUserIsOnline()
    {
        $this->testUser->setLastAction(time() - 50);
        $this->testUser->save();
        $this->assertContains("testuser1", getUsersOnline());
    }

    public function testGetUsersOnlineUserIsNotOnline()
    {
        $this->testUser->setLastAction(null);
        $this->testUser->save();
        $this->assertNotContains("testuser1", getUsersOnline());
    }

    public function testGetUserByNameUserExists()
    {
        $user = getUserByName("testuser1");
        
        $this->assertEquals($this->testUser->getID(), $user["id"]);
        $this->assertEquals("testuser1", $user["username"]);
        $this->assertEquals("Muster", $user["lastname"]);
        $this->assertEquals("Max", $user["firstname"]);
        $this->assertEquals("testuser1", $user["username"]);
    }

    public function testGetUserByNameUserNotExists()
    {
        $user = getUserByName("slenderman");
        
        $this->assertFalse($user);
    }

    public function testGetUserByIdUserExists()
    {
        $user = getUserById($this->testUser->getID());
        
        $this->assertEquals($this->testUser->getID(), $user["id"]);
        $this->assertEquals("testuser1", $user["username"]);
        $this->assertEquals("Muster", $user["lastname"]);
        $this->assertEquals("Max", $user["firstname"]);
        $this->assertEquals("testuser1", $user["username"]);
    }

    public function testGetUserByIdUserNotExists()
    {
        $user = getUserById(PHP_INT_MAX);
        
        $this->assertFalse($user);
    }

    public function testGetAllUsers()
    {
        $allUsers = getAllUsers();
        foreach ($allUsers as $user) {
            if ($user->username == "testuser1") {
                $this->assertEquals($user->id, $this->testUser->getID());
                return;
            }
        }
        $this->fail("The testuser is not in the result.");
    }

    public function testGetUsers()
    {
        $allUsers = getUsers();
        foreach ($allUsers as $user) {
            if ($user->username == "testuser1") {
                $this->assertEquals($user->id, $this->testUser->getID());
                return;
            }
        }
        $this->fail("The testuser is not in the result.");
    }

    public function testAddUser()
    {
        $this->assertFalse(user_exists("testuser2"));
        
        adduser("testuser2", "Kolumna", "Karla", "karla.kolumna@presse.de", "oldpassword", false);
        $this->assertTrue(user_exists("testuser2"));
    }

    public function testChangePassword()
    {
        $user = new User();
        $user->loadByUsername("testuser3");
        $id = $user->getId();
        
        $this->assertTrue(is_array(validate_login("testuser3", "oldpassword")));
        
        changePassword("newpassword", $id);
        
        $this->assertTrue(is_array(validate_login("testuser3", "newpassword")));
    }
}