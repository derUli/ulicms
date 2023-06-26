<?php

use App\Backend\Menu\AdminMenu;
use App\Backend\Menu\MenuEntry;

class AdminMenuTest extends \PHPUnit\Framework\TestCase {
    private $adminUser;

    private $limitedUser;

    private $testGroup;

    protected function setUp(): void {
        $adminUser = new User();
        $adminUser->setUsername('testuser-admin');
        $adminUser->setLastname('Admin');
        $adminUser->setFirstname('Der');
        $adminUser->setPassword(uniqid());
        $adminUser->setAdmin(true);
        $adminUser->save();
        $this->adminUser = $adminUser;

        $group = new Group();
        $group->addPermission('info', true);
        $group->save();
        $this->testGroup = $group;

        $limitedUser = new User();
        $limitedUser->setUsername('testuser-nichtadmin');
        $limitedUser->setLastname('Admin');
        $limitedUser->setFirstname('Nicht');
        $limitedUser->setPassword(uniqid());
        $limitedUser->setAdmin(false);
        $limitedUser->addSecondaryGroup($group);
        $limitedUser->save();
        $this->limitedUser = $limitedUser;
    }

    protected function tearDown(): void {
        $this->adminUser->delete();
        $this->limitedUser->delete();
        $this->testGroup->delete();
    }

    public function testGetChildren(): void {
        $menu = new AdminMenu($this->getMenuEntries());
        $this->assertEquals($this->getMenuEntries(), $menu->getChildren());
    }

    public function testSetChildren(): void {
        $menu = new AdminMenu($this->getMenuEntries());

        $menu->setChildren(
            [
                new MenuEntry(
                    'Foobar',
                    'https://foobar.com',
                    'foobar',
                    [],
                    [],
                    false
                )
            ]
        );
        $this->assertCount(1, $menu->getChildren());
        $this->assertEquals('foobar', $menu->getChildren()[0]->getIdentifier());
    }

    public function testHasChildrenReturnsTrue(): void {
        $menu = new AdminMenu($this->getMenuEntries());
        $this->assertTrue($menu->hasChildren());
    }

    public function testHasChildrenReturnsFalse(): void {
        $menu = new AdminMenu($this->getMenuEntries());
        $menu->setChildren([]);
        $this->assertFalse($menu->hasChildren());
    }

    public function testRenderAsAdmin(): void {
        $inputExpected = file_get_contents(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/tests/fixtures/menu/admin_menu/render_as_admin.html'
            )
        );
        $menu = new AdminMenu($this->getMenuEntries());

        $this->adminUser->registerSession(false);
        $this->assertEquals($inputExpected, $menu->render());
    }

    public function testRenderAsUserWithLimitedPermissions(): void {
        $inputExpected = file_get_contents(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/tests/fixtures/menu/admin_menu/render_as_user.html'
            )
        );
        $menu = new AdminMenu($this->getMenuEntries());
        $this->limitedUser->registerSession(false);

        $this->assertEquals($inputExpected, $menu->render());
    }

    /**
     * @return MenuEntry[]
     */
    private function getMenuEntries(): array {
        return [
            new MenuEntry(
                'Say Hello',
                'https://www.hello-world.com/',
                'say_hello',
                ['info', 'dashboard'],
                [],
                true
            ),
            new MenuEntry(
                'Google',
                'https://google.de',
                'google',
                ['google'],
                [],
                false
            ),
            new MenuEntry(
                'UliCMS',
                'https://ulicms.de',
                'ulicms',
                [],
                [],
                false
            )
        ];
    }
}
