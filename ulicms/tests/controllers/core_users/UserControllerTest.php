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
           $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = "80";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo.html";
    }

    protected function tearDown(): void {
        LoggerRegistry::unregister("audit_log");
        Database::deleteFrom("users", "username like 'testuser-%'");

        $_SESSION = [];
        $_POST = [];
        $_SERVER = [];
    }

    public function getPostVars(): array {
        $groups = Group::getAll();
        $groupIds = array_map(function($value) {
            return $value->getId();
        }, $groups);
        return [
            "username" => "testuser-john",
            "firstname" => "John",
            "lastname" => "Doe",
            "password" => "topsecret",
            "email" => "johndoe@example.org",
            "default_language" => "en",
            "locked" => "1",
            "group_id" => $groups[0]->getId(),
            "require_password_change" => "1",
            "secondary_groups" => $groupIds
        ];
    }

    public function testCreate() {
       $groups = Group::getAll();
        $groupIds = array_map(function($value) {
            return $value->getId();
        }, $groups);

        $_POST = $this->getPostVars();

        $controller = new UserController();
        $user = $controller->_createPost();

        $this->assertGreaterThanOrEqual(1, $user->getId());

        $user->reload();
        $this->assertEquals("testuser-john", $user->getUsername());
        $this->assertEquals("John", $user->getFirstname());
        $this->assertEquals("Doe", $user->getLastname());
        $this->assertGreaterThanOrEqual(128, strlen($user->getPassword()));
        $this->assertEquals("johndoe@example.org", $user->getEmail());
        $this->assertEquals("en", $user->getDefaultLanguage());
        $this->assertTrue($user->isLocked());
        $this->assertGreaterThanOrEqual(1, $user->getGroupId());
        $this->assertTrue($user->getRequirePasswordChange());
        $this->assertCount(count($groupIds), $user->getSecondaryGroups());
    }

    public function testCreateWithMail() {
        $groups = Group::getAll();
        $groupIds = array_map(function($value) {
            return $value->getId();
        }, $groups);

        $_POST = $this->getPostVars();
        $_POST["send_mail"] = "1";

        $controller = new UserController();
        $user = $controller->_createPost();

        $this->assertGreaterThanOrEqual(1, $user->getId());

        $user->reload();
        $this->assertEquals("testuser-john", $user->getUsername());
        $this->assertEquals("John", $user->getFirstname());
        $this->assertEquals("Doe", $user->getLastname());
        $this->assertGreaterThanOrEqual(128, strlen($user->getPassword()));
        $this->assertEquals("johndoe@example.org", $user->getEmail());
        $this->assertEquals("en", $user->getDefaultLanguage());
        $this->assertTrue($user->isLocked());
        $this->assertGreaterThanOrEqual(1, $user->getGroupId());
        $this->assertTrue($user->getRequirePasswordChange());
        $this->assertCount(count($groupIds), $user->getSecondaryGroups());
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
