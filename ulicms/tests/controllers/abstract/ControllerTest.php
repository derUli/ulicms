<?php

use App\Exceptions\AccessDeniedException;

class ControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_SESSION = [];

        Translation::loadAllModuleLanguageFiles('en');
    }

    protected function tearDown(): void
    {
        $_REQUEST = [];
        $_SERVER = [];
        $_SESSION = [];
        ViewBag::delete("sample_text");
        Database::deleteFrom("users", "username like 'testuser-%'");
    }

    public function testCallNonExistingMethod()
    {
        $controller = new PageController();

        $_REQUEST["sClass"] = PageController::class;
        $_REQUEST["sMethod"] = "puke";

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("method puke is not callable");
        $controller->runCommand();
    }

    public function testCallExistingPostMethodAccessDenied()
    {
        $controller = new PageController();

        $_REQUEST["sClass"] = PageController::class;
        $_REQUEST["sMethod"] = "create";

        $this->expectException(AccessDeniedException::class);
        $controller->runCommand();
    }

    public function testCallExistingMethodAccessDenied()
    {
        $controller = new PageController();

        $_REQUEST["sClass"] = PageController::class;
        $_REQUEST["sMethod"] = "pages";

        $this->expectException(AccessDeniedException::class);
        $controller->runCommand();
    }

    public function testCallPostMethod()
    {
        $user = $this->getAdminUser();
        $_SESSION["login_id"] = $user->getId();

        $controller = new Fortune();

        $_REQUEST["sClass"] = Fortune::class;
        $_REQUEST["sMethod"] = "doSomething";

        $controller->runCommand();

        $this->assertEquals(
            "This is POST answer.",
            ViewBag::get("sample_text")
        );
    }

    public function testCallHeadMethod()
    {
        $user = $this->getAdminUser();
        $_SESSION["login_id"] = $user->getId();

        $controller = new Fortune();

        $_SERVER["REQUEST_METHOD"] = "put";
        $_REQUEST["sClass"] = Fortune::class;
        $_REQUEST["sMethod"] = "doSomething";

        $controller->runCommand();

        $this->assertEquals(
            "Unkwown Request Method",
            ViewBag::get("sample_text")
        );
    }

    protected function getAdminUser(): User
    {
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
