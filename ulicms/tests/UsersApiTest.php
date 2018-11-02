<?php

class UsersApiTest extends \PHPUnit\Framework\TestCase
{

    private $testUser;

    private $testGroup;

    public function setUp()
    {
        @session_destroy();
        @session_start();
        unset($_SESSION["login_id"]);
        unset($_SESSION["logged_in"]);
        
        $group = new Group();
        $group->setName("testgroup");
        $group->save();
        $this->testGroup = $group;
        
        $user = new User();
        $user->setUsername("testuser1");
        $user->setLastname("Muster");
        $user->setFirstname("Max");
        $user->setEmail("max@muster.de");
        $user->setPassword("topsecret");
        $user->setGroup($this->testGroup);
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
        
        $this->testGroup->delete();
        $this->testUser->delete();
        
        $user = new User();
        $user->loadByUsername("testuser2");
        $user->delete();
        
        $user = new User();
        $user->loadByUsername("testuser3");
        $user->delete();
        
        @session_destroy();
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

    public function testRegisterSession()
    {
        $login = validate_login("testuser1", "topsecret");
        register_session($login, false);
        
        $this->assertEquals("testuser1", $_SESSION["ulicms_login"]);
        $this->assertEquals("Muster", $_SESSION["lastname"]);
        $this->assertEquals("Max", $_SESSION["firstname"]);
        $this->assertEquals("max@muster.de", $_SESSION["email"]);
        
        $this->assertGreaterThan(0, $this->testUser->getId());
        $this->assertEquals($this->testUser->getId(), $_SESSION["login_id"]);
        
        $this->assertGreaterThan(0, $this->testGroup->getId());
        $this->assertEquals($this->testGroup->getId(), $_SESSION["group_id"]);
        
        $this->assertEquals(0, $_SESSION["require_password_change"]);
        $this->assertTrue($_SESSION["logged_in"]);
        
        $this->assertGreaterThanOrEqual(time() - 100, $_SESSION["session_begin"]);
    }
}