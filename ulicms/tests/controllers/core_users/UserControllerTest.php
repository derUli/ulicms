<?php

class UserControllerTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        LoggerRegistry::register(
                "audit_log",
                new Logger(Path::resolve("ULICMS_LOG/audit_log"))
        );

        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $_SESSION = [
            "login_id" => $user->getId()
        ];
    }

    protected function tearDown(): void {
        LoggerRegistry::unregister("audit_log");
        Database::deleteFrom("users", "username = 'testuser-ist-admin'");

        $_SESSION = [];
    }

    public function testDeleteUserReturnsTrue() {
        $controller = new UserController();
        $testUser = $this->getTestUser();
        $success = $controller->_deletePost($testUser->getId());
        $this->assertTrue($success);

        $testUser = new User();
        $testUser->loadByUsername("testuser-ist-admin");
        $this->assertFalse($testUser->isPersistent());
    }

    public function testDeleteUserReturnsFalse() {
        $controller = new UserController();
        $success = $controller->_deletePost(PHP_INT_MAX);
        $this->assertFalse($success);
    }

    protected function getTestUser(): User {
        $user = new User();
        $user->setUsername("testuser-ist-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Ist");
        $user->setPassword(uniqid());
        $user->setAdmin(true);
        $user->save();

        return $user;
    }

}
