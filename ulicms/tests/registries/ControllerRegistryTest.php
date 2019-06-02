<?php

class ControllerRegistryTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        if (session_id()) {
            session_destroy();
        }
        @session_start();
    }

    public function tearDown() {
        if (session_id()) {
            @session_destroy();
        }

        Database::query("delete from {prefix}users where username like 'testuser-%", true);
    }

    public function testGet() {
        $this->assertInstanceOf(CommentsController::class, ControllerRegistry::get("CommentsController"));
    }

    public function testUserCanCallNotLoggedIn() {
        $this->assertFalse(ControllerRegistry::userCanCall("PageController", "createPost"));
    }

    public function testUserCanCallReturnsTrue() {
        $user = new User();
        $user->setUsername("testuser-nicht-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Nicht");
        $user->setPassword(uniqid());
        $user->setAdmin(true);
        $user->save();

        $_SESSION["login_id"] = $user->getId();

        $this->assertTrue(ControllerRegistry::userCanCall("PageController", "createPost"));
        unset($_SESSION["login_id"]);
    }

    public function testUserCanCallReturnsFalse() {
        $user = new User();
        $user->setUsername("testuser-nicht-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Nicht");
        $user->setPassword(uniqid());
        $user->setAdmin(false);
        $user->save();

        $_SESSION["login_id"] = $user->getId();

        $this->assertFalse(ControllerRegistry::userCanCall("PageController", "createPost"));
        unset($_SESSION["login_id"]);
    }

}
