<?php

use UliCMS\Registries\ActionRegistry;
use UliCMS\Models\Users\User;
use UliCMS\Helpers\BackendHelper;

class ControllerRegistryTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        ControllerRegistry::loadModuleControllers();

        if (!session_id() && !headers_sent()) {
            
        }
        ActionRegistry::loadModuleActionAssignment();
    }

    protected function tearDown(): void {
        if (session_id() && !headers_sent()) {
            
        }

        Database::query("delete from {prefix}users where username like 'testuser-%'", true);
        unset($_REQUEST["sClass"]);
        unset($_REQUEST["sMethod"]);
        unset($_SERVER["REQUEST_METHOD"]);
    }

    public function testGetWithClassNameReturnsController() {
        $this->assertInstanceOf(
                CommentsController::class,
                ControllerRegistry::get("CommentsController")
        );
    }

    public function testGetWithClassNameReturnsNull() {
        $this->assertNull(ControllerRegistry::get("GibtsNichtController"));
    }

    public function testGetWithActionReturnsController() {
        BackendHelper::setAction("audio");
        $this->assertInstanceOf(
                AudioController::class,
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

    public function testUserCanCallWildCard() {
        $user = new User();
        $user->setUsername("testuser-nicht-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Nicht");
        $user->setPassword(uniqid());
        $user->setAdmin(false);
        $user->save();

        $_SESSION["login_id"] = $user->getId();

        $this->assertFalse(
                ControllerRegistry::userCanCall(
                        "HomeController",
                        "newsfeed"
                )
        );
        unset($_SESSION["login_id"]);
    }

    public function testRunMethodsWithNonExistingClassName() {
        $_REQUEST["sClass"] = "GibtsNichtController";
        $_REQUEST["sMethod"] = "puke";

        $this->expectException(BadMethodCallException::class);
        ControllerRegistry::runMethods();
    }

    public function testRunMethods() {
        $_REQUEST["sClass"] = "Fortune";
        $_REQUEST["sMethod"] = "helloWorld";
        $_SERVER["REQUEST_METHOD"] = "GET";

        ob_start();
        ControllerRegistry::runMethods();

        $this->assertEquals('Hello World!', ob_get_clean());
    }

}
