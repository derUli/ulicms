<?php

class GroupTest extends \PHPUnit\Framework\TestCase {

    private $savedSettings = [];

    public function setUp() {

        $settings = array(
            "default_acl_group"
        );
        foreach ($settings as $setting) {
            $this->savedSettings[$setting] = Settings::get($setting);
        }

        require_once getLanguageFilePath("en");

        @session_start();
    }

    public function tearDown() {
        Database::query("delete from `{prefix}groups` where name = 'bla'", true);

        foreach ($this->savedSettings as $key => $value) {
            Settings::set($key, $value);
        }
        @session_destroy();
        unset($_SESSION["login_id"]);
    }

    public function testCreateGroup() {
        $group = new Group();
        $this->assertNull($group->getId());
        $group->setName("bla");
        $this->assertEquals("bla", $group->getName());
        $group->save();

        $oldID = $group->getId();
        $this->assertNotNull($oldID);
        $group = new Group($oldID);
        $this->assertEquals($oldID, $group->getId());
        $this->assertEquals("bla", $group->getName());
        $this->assertTrue(is_array($group->getPermissions()));
        $this->assertTrue(count($group->getPermissions()) >= 2);

        $group->setName("Hello");
        $group->save();

        $group = new Group($oldID);
        $this->assertEquals("Hello", $group->getName());

        $group->delete();
        $this->assertNull($group->getId());

        // delete an already deleted element should do nothing
        $group->delete();

        $group = new Group($oldID);
        $this->assertNull($group->getId());
    }

    public function testGetUsers() {
        $group = new Group(1);
        $this->assertTrue(count($group->getUsers()) >= 1);
    }

    public function testGetCurrentGroupId() {
        $_SESSION["group_id"] = 1;
        $this->assertEquals(1, Group::getCurrentGroupId());
    }

    public function testGetCurrentGroup() {
        $_SESSION["group_id"] = 1;

        $group = Group::getCurrentGroup();
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals(1, $group->getId());
        $this->assertFalse(StringHelper::isNullOrWhitespace($group->getName()));
    }

    public function testGetCurrentGroupReturnsNull() {
        $this->assertNull(Group::getCurrentGroup());
    }

    public function testGetPrimaryGroupIdReturnsNull() {
        Settings::delete("default_acl_group");
        $this->assertNull(Group::getDefaultPrimaryGroupId());
    }

    public function testGetPrimaryGroupReturnsNull() {
        Settings::delete("default_acl_group");
        $this->assertNull(Group::getDefaultPrimaryGroup());
    }

    public function testGetPrimaryGroupIdReturnsId() {
        Settings::set("default_acl_group", "1");
        $this->assertEquals(1, Group::getDefaultPrimaryGroupId());
    }

    public function testGetPrimaryGroupReturnsGroup() {
        Settings::set("default_acl_group", "1");

        $group = Group::getDefaultPrimaryGroup();
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals(1, $group->getId());
        $this->assertFalse(StringHelper::isNullOrWhitespace($group->getName()));
    }

    public function testSetPermissions() {
        $group = new Group();
        $group->setPermissions(["foo", "bar"]);
        $this->assertEquals(
                ["foo", "bar"],
                $group->getPermissions()
        );
    }

    public function testAddPermission() {
        $group = new Group();
        $group->setPermissions(["foo" => true, "bar" => false]);
        $group->removePermission("foo");

        $this->assertEquals(["bar" => false], $group->getPermissions());
    }

    public function testSetAllowableTags() {
        $group = new Group();
        $group->setAllowableTags("<strong><p><i><em><a><ul><li><ol>");

        $this->assertEquals("<strong><p><i><em><a><ul><li><ol>",
                $group->getAllowableTags());
    }

    public function testGetIdReturnsNull() {
        $group = new Group();
        $this->assertNull($group->getId());
    }

    public function testSetAndGetIdIdReturnsNull() {
        $group = new Group();
        $group->setId(123);
        $this->assertEquals(123, $group->getId());
    }

}
