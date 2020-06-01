<?php

class FormControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
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

    public function tearDown() {
        LoggerRegistry::unregister("audit_log");
        Database::deleteFrom("users", "username = 'testuser-ist-admin'");

        $_SESSION = [];
    }

    public function testDeleteReturnsTrue() {
        $id = $this->createTestForm();
        $controller = new FormController();
        $success = $controller->_deletePost($id);

        $this->assertTrue($success);
    }

    protected function createTestForm(): int {
        $page = ContentFactory::getAllRegular()[0];
        Forms::createForm(
                "Unit Test 2",
                "max@muster.de",
                "Subject 1",
                1,
                "message=>Message",
                "message",
                "email",
                $page->getId(),
                true
        );
        return Database::getInsertID();
    }

}
