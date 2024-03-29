<?php

use App\Backend\Menu\MenuEntry;

class MenuEntryTest extends \PHPUnit\Framework\TestCase {
    private $permittedUser;

    private $notPermittedUser;

    private $testGroup;

    protected function setUp(): void {
        $group = new Group();
        $group->addPermission('info', true);
        $group->save();

        $permittedUser = new User();
        $permittedUser->setUsername('testuser-admin');
        $permittedUser->setLastname('Admin');
        $permittedUser->setFirstname('Der');
        $permittedUser->setPassword(uniqid());
        $permittedUser->setPrimaryGroup($group);
        $permittedUser->save();
        $this->permittedUser = $permittedUser;

        $this->testGroup = $group;

        $notPermittedUser = new User();
        $notPermittedUser->setUsername('testuser-nichtadmin');
        $notPermittedUser->setLastname('Admin');
        $notPermittedUser->setFirstname('Nicht');
        $notPermittedUser->setPassword(uniqid());
        $notPermittedUser->save();
        $this->notPermittedUser = $notPermittedUser;
    }

    protected function tearDown(): void {
        $this->permittedUser->delete();
        $this->notPermittedUser->delete();
        $this->testGroup->delete();

        unset($_GET['action'], $_REQUEST['action']);

    }

    public function testConstructor(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $this->assertEquals('Say Hello', $menuEntry->getTitle());
        $this->assertEquals(
            'https://www.hello-world.com/',
            $menuEntry->getLink()
        );
        $this->assertEquals('say_hello', $menuEntry->getIdentifier());
        $this->assertEquals(
            ['info', 'dashboard'],
            $menuEntry->getPermissions()
        );
        $this->assertCount(0, $menuEntry->getChildren());
        $this->assertTrue($menuEntry->getNewWindow());
    }

    public function testHasChildrenReturnsTrue(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $menuEntry->setChildren([
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren()
        ]);
        $this->assertTrue($menuEntry->hasChildren());
    }

    public function testHasChildrenReturnsFalse(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $this->assertFalse($menuEntry->hasChildren());
    }

    public function testSetTitle(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setTitle('Foobar');
        $this->assertEquals('Foobar', $menuEntry->getTitle());
    }

    public function testSetPermissions(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setPermissions(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $menuEntry->getPermissions());
    }

    public function testSetLink(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setLink('https://www.ulicms.de');
        $this->assertEquals('https://www.ulicms.de', $menuEntry->getLink());
    }

    public function testSetIdentifier(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setIdentifier('foobar');
        $this->assertEquals('foobar', $menuEntry->getIdentifier());
    }

    public function testSetChildren(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setChildren([
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren()
        ]);
        $this->assertCount(3, $menuEntry->getChildren());
    }

    public function testAddChild(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setChildren([
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren(),
            $this->constructMenuEntryWithoutChildren()
        ]);

        $menuEntry->addChild(
            $this->constructMenuEntryWithoutChildren()

        );
        $this->assertCount(4, $menuEntry->getChildren());
    }

    public function testAddChildren(): void {
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

    public function testSetNewWindows(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(false);
        $this->assertFalse($menuEntry->getNewWindow());
    }

    public function testUserHasPermissionReturnsTrueWithPermission(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $this->permittedUser->registerSession(false);
        $this->assertTrue($menuEntry->userHasPermission());
    }

    public function testUserHasPermissionReturnsTrueWithoutPermissions(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $this->permittedUser->registerSession(false);
        $menuEntry->setPermissions(null);
        $this->assertTrue($menuEntry->userHasPermission());
    }

    public function testUserHasPermissionReturnsFalse(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();

        $menuEntry->setPermissions('foobar');
        $this->notPermittedUser->registerSession(false);
        $this->assertFalse($menuEntry->userHasPermission());
    }

    public function testRender(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(false);

        $inputExpected = file_get_contents(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/tests/fixtures/menu/menu_entry/render.html'
            )
        );
        $this->assertEquals($inputExpected, $menuEntry->render());
    }

    public function testRenderWithNewWindow(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(true);

        $inputExpected = file_get_contents(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/tests/fixtures/menu/menu_entry/render_with_new_window.html'
            )
        );
        $this->assertEquals($inputExpected, $menuEntry->render());
    }

    public function testRenderWithCurrentPage(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $menuEntry->setNewWindow(false);

        \App\Helpers\BackendHelper::setAction('say_hello');

        $inputExpected = file_get_contents(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/tests/fixtures/menu/menu_entry/render_current_page.html'
            )
        );
        $this->assertEquals($inputExpected, $menuEntry->render());
    }

    public function testSetIsAjax(): void {
        $menuEntry = $this->constructMenuEntryWithoutChildren();
        $this->assertFalse($menuEntry->getIsAjax());

        $menuEntry->setIsAjax(true);
        $this->assertTrue($menuEntry->getIsAjax());

        $menuEntry->setIsAjax(false);
        $this->assertFalse($menuEntry->getIsAjax());
    }

    private function constructMenuEntryWithoutChildren(): MenuEntry {
        return new MenuEntry(
            'Say Hello',
            'https://www.hello-world.com/',
            'say_hello',
            ['info', 'dashboard'],
            [],
            true
        );
    }
}
