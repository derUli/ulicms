<?php

class GroupTest extends \PHPUnit\Framework\TestCase {
    private $savedSettings = [];

    protected function setUp(): void {
        $settings = [
            'default_acl_group'
        ];
        foreach ($settings as $setting) {
            $this->savedSettings[$setting] = Settings::get($setting);
        }

        require_once getLanguageFilePath('en');
    }

    protected function tearDown(): void {
        Database::query("delete from `{prefix}groups` where name = 'bla'", true);

        foreach ($this->savedSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testCreateGroup(): void {
        $group = new Group();
        $this->assertNull($group->getId());
        $group->setName('bla');
        $this->assertEquals('bla', $group->getName());
        $group->save();

        $oldID = $group->getId();
        $this->assertNotNull($oldID);
        $group = new Group($oldID);
        $this->assertEquals($oldID, $group->getId());
        $this->assertEquals('bla', $group->getName());
        $this->assertTrue(is_array($group->getPermissions()));
        $this->assertGreaterThanOrEqual(58, count($group->getPermissions()));

        $group->setName('Hello');
        $group->save();

        $group = new Group($oldID);
        $this->assertEquals('Hello', $group->getName());

        $group->delete();
        $this->assertNull($group->getId());

        // delete an already deleted element should do nothing
        $group->delete();

        $group = new Group($oldID);
        $this->assertNull($group->getId());
    }

    public function testGetUsers(): void {
        $group = new Group(1);
        $this->assertTrue(count($group->getUsers()) >= 1);
    }

    public function testGetCurrentGroupId(): void {
        $_SESSION['group_id'] = 1;
        $this->assertEquals(1, Group::getCurrentGroupId());
    }

    public function testGetCurrentGroup(): void {
        $_SESSION['group_id'] = 1;

        $group = Group::getCurrentGroup();
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals(1, $group->getId());
        $this->assertFalse(empty($group->getName()));
    }

    public function testGetCurrentGroupReturnsNull(): void {
        $this->assertNull(Group::getCurrentGroup());
    }

    public function testGetPrimaryGroupIdReturnsNull(): void {
        Settings::delete('default_acl_group');
        $this->assertNull(Group::getDefaultPrimaryGroupId());
    }

    public function testGetPrimaryGroupReturnsNull(): void {
        Settings::delete('default_acl_group');
        $this->assertNull(Group::getDefaultPrimaryGroup());
    }

    public function testGetPrimaryGroupIdReturnsId(): void {
        Settings::set('default_acl_group', '1');
        $this->assertEquals(1, Group::getDefaultPrimaryGroupId());
    }

    public function testGetPrimaryGroupReturnsGroup(): void {
        Settings::set('default_acl_group', '1');

        $group = Group::getDefaultPrimaryGroup();
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals(1, $group->getId());
        $this->assertFalse(empty($group->getName()));
    }

    public function testSetPermissions(): void {
        $group = new Group();
        $group->setPermissions(['foo', 'bar']);
        $this->assertEquals(
            ['foo', 'bar'],
            $group->getPermissions()
        );
    }

    public function testAddPermission(): void {
        $group = new Group();
        $group->setPermissions(['foo' => true, 'bar' => false]);
        $group->removePermission('foo');

        $this->assertEquals(['bar' => false], $group->getPermissions());
    }

    public function testSetAllowableTags(): void {
        $group = new Group();
        $group->setAllowableTags('<strong><p><i><em><a><ul><li><ol>');

        $this->assertEquals(
            '<strong><p><i><em><a><ul><li><ol>',
            $group->getAllowableTags()
        );
    }

    public function testGetIdReturnsNull(): void {
        $group = new Group();
        $this->assertNull($group->getId());
    }

    public function testSetAndGetIdIdReturnsNull(): void {
        $group = new Group();
        $group->setId(123);
        $this->assertEquals(123, $group->getId());
    }
}
