<?php

class FormControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $manager = new UserManager();
        $user = $manager->getAllUsers("admin desc")[0];

        $_SESSION = [
            "login_id" => $user->getId()
        ];

        $_POST = [];
    }

    protected function tearDown(): void
    {
        Database::deleteFrom("users", "username = 'testuser-ist-admin'");
        Database::deleteFrom("forms", "name like 'Unit Test%'");

        $_SESSION = [];
        $_POST = [];
    }

    public function testDeleteReturnsTrue()
    {
        $id = $this->createTestForm();
        $controller = new FormController();
        $success = $controller->_deletePost($id);

        $this->assertTrue($success);
    }

    protected function createTestForm(): int
    {
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

    public function testCreatePostReturnsId()
    {
        $this->setPostVars();
        $controller = new FormController();
        $id = $controller->_createPost();
        $this->assertIsNumeric($id);
        $this->assertGreaterThanOrEqual(1, $id);
    }

    protected function setPostVars()
    {
        $page = ContentFactory::getAllRegular()[0];

        $_POST['name'] = "Unit Test " . time();
        $_POST["enabled"] = "1";
        $_POST["email_to"] = "foo@example.invalid";
        $_POST["subject"] = "My Subject";
        $_POST["category_id"] = "1";
        $_POST["fields"] = "foo=>bar";
        $_POST["required_fields"] = "foo";
        $_POST["mail_from_field"] = "foo";
        $_POST["target_page_id"] = (string)$page->getId();
    }

    public function testUpdatePostReturnsTrue()
    {
        $this->setPostVars();

        $controller = new FormController();
        $id = $controller->_createPost();

        $_POST['id'] = $id;
        $_POST['name'] = "Unit Test Updated";

        $success = $controller->_updatePost();
        $this->assertTrue($success);
    }

    public function testUpdatePostReturnsFalse()
    {
        $this->setPostVars();

        $_POST['id'] = PHP_INT_MAX;

        $controller = new FormController();
        $success = $controller->_updatePost();

        $this->assertFalse($success);
    }
}
