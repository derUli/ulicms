<?php

use App\Registries\ActionRegistry;

class ControllerRegistryTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $moduleManager = new \App\Packages\ModuleManager();
        $moduleManager->sync();

        ControllerRegistry::loadModuleControllers();

        ActionRegistry::loadModuleActionAssignment();
    }

    protected function tearDown(): void {
        Database::query("delete from {prefix}users where username like 'testuser-%'", true);
        unset($_REQUEST['sClass'], $_REQUEST['sMethod'], $_SERVER['REQUEST_METHOD']);

    }

    public function testGetWithClassNameReturnsController(): void {
        $this->assertInstanceOf(
            CommentsController::class,
            ControllerRegistry::get('CommentsController')
        );
    }

    public function testGetWithClassNameReturnsNull(): void {
        $this->assertNull(ControllerRegistry::get('GibtsNichtController'));
    }

    public function testGetWithActionReturnsController(): void {
        \App\Helpers\BackendHelper::setAction('audio');
        $this->assertInstanceOf(
            AudioController::class,
            ControllerRegistry::get()
        );

        \App\Helpers\BackendHelper::setAction('home');
    }

    public function testGetWithNonExistingActionReturnsNull(): void {
        \App\Helpers\BackendHelper::setAction('pages');
        $this->assertNull(
            ControllerRegistry::get()
        );

        \App\Helpers\BackendHelper::setAction('home');
    }

    public function testGetReturnsNull(): void {
        \App\Helpers\BackendHelper::setAction('info');
        $this->assertNull(
            ControllerRegistry::get()
        );
    }

    public function testUserCanCallNotLoggedIn(): void {
        $this->assertFalse(
            ControllerRegistry::userCanCall('PageController', 'createPost')
        );
    }

    public function testUserCanCallReturnsTrue(): void {
        $user = new User();
        $user->setUsername('testuser-nicht-admin');
        $user->setLastname('Admin');
        $user->setFirstname('Nicht');
        $user->setPassword(uniqid());
        $user->setAdmin(true);
        $user->save();

        $_SESSION['login_id'] = $user->getId();

        $this->assertTrue(ControllerRegistry::userCanCall('PageController', 'createPost'));
        unset($_SESSION['login_id']);
    }

    public function testUserCanCallReturnsFalse(): void {
        $user = new User();
        $user->setUsername('testuser-nicht-admin');
        $user->setLastname('Admin');
        $user->setFirstname('Nicht');
        $user->setPassword(uniqid());
        $user->setAdmin(false);
        $user->save();

        $_SESSION['login_id'] = $user->getId();

        $this->assertFalse(ControllerRegistry::userCanCall('PageController', 'createPost'));
        unset($_SESSION['login_id']);
    }

    public function testUserCanCallWildCard(): void {
        $user = new User();
        $user->setUsername('testuser-nicht-admin');
        $user->setLastname('Admin');
        $user->setFirstname('Nicht');
        $user->setPassword(uniqid());
        $user->setAdmin(false);
        $user->save();

        $_SESSION['login_id'] = $user->getId();

        $this->assertFalse(
            ControllerRegistry::userCanCall(
                'HomeController',
                'newsfeed'
            )
        );
        unset($_SESSION['login_id']);
    }

    public function testRunMethodsWithNonExistingClassName(): void {
        $_REQUEST['sClass'] = 'GibtsNichtController';
        $_REQUEST['sMethod'] = 'puke';

        $this->expectException(BadMethodCallException::class);
        ControllerRegistry::runMethods();
    }

    public function testRunMethods(): void {
        $_REQUEST['sClass'] = 'Fortune';
        $_REQUEST['sMethod'] = 'helloWorld';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        ControllerRegistry::runMethods();

        $this->assertEquals('Hello World!', ob_get_clean());
    }
}
