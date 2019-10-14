<?php

class ControllerRegistryTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        if (session_id()) {
            session_destroy();
        }
        @session_start();
        ActionRegistry::loadModuleActionAssignment();
    }

    public function tearDown() {
        if (session_id()) {
            @session_destroy();
        }

        Database::query("delete from {prefix}users where username like 'testuser-%'", true);
    }

    public function testGetWithClassNameReturnsController() {
        $this->assertInstanceOf(CommentsController::class,
                ControllerRegistry::get("CommentsController"));
    }

    public function testGetWithActionReturnsController() {
        BackendHelper::setAction("audio");
        $this->assertInstanceOf(AudioController::class,
                ControllerRegistry::get()
        );

        BackendHelper::setAction("home");
    }

    public function testGetWithNonExistingActionReturnsNull() {
        BackendHelper::setAction("pages");
        $this->assertNull(
                ControllerRegistry::get()
        );

        BackendHelper::setAction("home");
    }

    public function testGetReturnsNull() {
        BackendHelper::setAction("info");
        $this->assertNull(
                ControllerRegistry::get()
        );
    }

    public function testUserCanCallNotLoggedIn() {
        $this->assertFalse(
                ControllerRegistry::userCanCall("PageController", "createPost")
        );
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
