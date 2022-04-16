<?php

use UliCMS\Backend\Menu\MenuEntry;
use UliCMS\Models\Users\User;
use UliCMS\Models\Users\Group;
use UliCMS\Helpers\BackendHelper;

class MenuEntryTest extends \PHPUnit\Framework\TestCase {

    private $permittedUser;
    private $notPermittedUser;
    private $testGroup;

    protected function setUp(): void {
        $group = new Group();
        $group->addPermission("info", true);
        $group->save();

        $permittedUser = new User();
        $permittedUser->setUsername("testuser-admin");
        $permittedUser->setLastname("Admin");
        $permittedUser->setFirstname("Der");
        $permittedUser->setPassword(uniqid());
        $permittedUser->setPrimaryGroup($group);
        $permittedUser->save();
        $this->permittedUser = $permittedUser;

        $this->testGroup = $group;

        $notPermittedUser = new User();
        $notPermittedUser->setUsername("testuser-nichtadmin");
        $notPermittedUser->setLastname("Admin");
        $notPermittedUser->setFirstname("Nicht");
        $notPermittedUser->setPassword(uniqid());
        $notPermittedUser->save();
        $this->notPermittedUser = $notPermittedUser;
    }

    protected function tearDown(): void {
        $this->permittedUser->delete();
        $this->notPermittedUser->delete();
        $this->testGroup->delete();

        unset($_GET["action"]);
        unset($_REQUEST["action"]);
    }

    private function constructMenuEntryWithoutChildren(): MenuEntry {
        return new MenuEntry(
                "Say Hello",
                "https://www.hello-world.com/",
                "say_hello",
                ["info", "dashboard"],
                [],
                true
        );
    }

    public function testConstructor() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $this->assertEquals("Say Hello", $menuEntry->getTitle());
        $this->assertEquals(
                "https://www.hello-world.com/",
                $menuEntry->getLink()
        );
        $this->assertEquals("say_hello", $menuEntry->getIdentifier());
        $this->assertEquals(
                ["info", "dashboard"],
                $menuEntry->getPermissions()
        );
        $this->assertCount(0, $menuEntry->getChildren());
        $this->assertTrue($menuEntry->getNewWindow());
    }

    public function testHasChildrenReturnsTrue() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $menuEntry->setChildren([
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren()
        ]);
        $this->assertTrue($menuEntry->hasChildren());
    }

    public function testHasChildrenReturnsFalse() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $this->assertFalse($menuEntry->hasChildren());
    }

    public function testSetTitle() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setTitle("Foobar");
        $this->assertEquals("Foobar", $menuEntry->getTitle());
    }

    public function testSetPermissions() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setPermissions(["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $menuEntry->getPermissions());
    }

    public function testSetLink() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setLink("https://www.ulicms.de");
        $this->assertEquals("https://www.ulicms.de", $menuEntry->getLink());
    }

    public function testSetIdentifier() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setIdentifier("foobar");
        $this->assertEquals("foobar", $menuEntry->getIdentifier());
    }

    public function testSetChildren() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setChildren([
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren()
        ]);
        $this->assertCount(3, $menuEntry->getChildren());
    }

    public function testAddChildren() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setChildren([
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren()
        ]);

        $menuEntry->addChildren(
                [
                    $this->constructMenuEntryWithoutChildren()
                ]
        );
        $this->assertCount(4, $menuEntry->getChildren());
    }

    public function testSetNewWindows() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(false);
        $this->assertFalse($menuEntry->getNewWindow());
    }

    public function testUserHasPermissionReturnsTrueWithPermission() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $this->permittedUser->registerSession(false);
        $this->assertTrue($menuEntry->userHasPermission());
    }

    public function testUserHasPermissionReturnsTrueWithoutPermissions() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $this->permittedUser->registerSession(false);
        $menuEntry->setPermissions(null);
        $this->assertTrue($menuEntry->userHasPermission());
    }

    public function testUserHasPermissionReturnsFalse() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $menuEntry->setPermissions("foobar");
        $this->notPermittedUser->registerSession(false);
        $this->assertFalse($menuEntry->userHasPermission());
    }

    public function testRender() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(false);

        $inputExpected = file_get_contents(
                Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/menu/menu_entry/render.html"
                )
        );
        $this->assertEquals($inputExpected, $menuEntry->render());
    }

    public function testRenderWithNewWindow() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(true);

        $inputExpected = file_get_contents(
                Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/menu/menu_entry/render_with_new_window.html"
                )
        );
        $this->assertEquals($inputExpected, $menuEntry->render());
    }

    public function testRenderWithCurrentPage() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(false);

        BackendHelper::setAction("say_hello");

        $inputExpected = file_get_contents(
                Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/menu/menu_entry/render_current_page.html"
                )
        );
        $this->assertEquals($inputExpected, $menuEntry->render());
    }

    public function testSetIsAjax() {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $this->assertFalse($menuEntry->getIsAjax());

        $menuEntry->setIsAjax(true);
        $this->assertTrue($menuEntry->getIsAjax());

        $menuEntry->setIsAjax(false);
        $this->assertFalse($menuEntry->getIsAjax());
    }

}
