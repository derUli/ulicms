<?php

class EntityPermissionsTest extends PHPUnit_Framework_TestCase
{

    private $testUser1;

    private $testUser2;

    private $testGroup1;

    private $testGroup2;

    public function setUp()
    {
        $user1 = new User();
        $user1->setUsername("max_muster");
        $user1->setFirstname("Max");
        $user1->setLastname("Muster");
        $user1->setGroupId(1);
        $user1->setPassword("secret");
        $user1->setLocked(1);
        $user1->save();
        $this->testUser1 = $user1;
        
        $user2 = new User();
        $user2->setUsername("john_doe");
        $user2->setFirstname("John");
        $user2->setLastname("Doe");
        $user2->setGroupId(1);
        $user2->setPassword("secret");
        $user2->setLocked(1);
        $user2->save();
        $this->testUser2 = $user2;
        
        $group1 = new Group();
        $group1->setName("Redakteure");
        $group1->save();
        $this->testGroup1 = $group1;
        $group2 = new Group();
        $group2->setName("Experten");
        $group2->save();
        $this->testGroup2 = $group2;
    }

    public function tearDown()
    {
        $this->testUser1->delete();
        $this->testUser2->delete();
        $this->testGroup1->delete();
        $this->testGroup2->delete();
        Database::query("delete from `{prefix}entity_permissions` where name = 'foo'");
    }

    public function testCreateEditAndDeletePermission()
    {
        $permission = new EntityPermissions("foo", 123);
        $permission->setOwnerUserId($this->testUser1->getId());
        $permission->setOwnerGroupId($this->testGroup1->getId());
        $this->assertEquals(123, $permission->getEntityId());
        $this->assertEquals("foo", $permission->getEntityName());
        $this->assertFalse($permission->getEditRestriction("admins"));
        $this->assertFalse($permission->getEditRestriction("group"));
        $this->assertFalse($permission->getEditRestriction("owner"));
        $this->assertFalse($permission->getEditRestriction("others"));
        $permission->save();
        
        $permission = new EntityPermissions("foo", 123);
        $this->assertEquals(123, $permission->getEntityId());
        $this->assertEquals("foo", $permission->getEntityName());
        $this->assertEquals($this->testUser1->getId(), $permission->getOwnerUserId());
        $this->assertEquals($this->testGroup1->getId(), $permission->getOwnerGroupId());
        $this->assertFalse($permission->getEditRestriction("admins"));
        $this->assertFalse($permission->getEditRestriction("group"));
        $this->assertFalse($permission->getEditRestriction("owner"));
        $this->assertFalse($permission->getEditRestriction("other"));
        
        $permission->setOwnerUserId($this->testUser2->getId());
        $permission->setOwnerGroupId($this->testGroup2->getId());
        $permission->setEditRestriction("admins", true);
        $permission->setEditRestriction("group", true);
        $permission->setEditRestriction("owner", true);
        $permission->setEditRestriction("others", false);
        $permission->save();
        
        $permission = new EntityPermissions("foo", 123);
        $this->assertEquals($this->testUser2->getId(), $permission->getOwnerUserId());
        $this->assertEquals($this->testGroup2->getId(), $permission->getOwnerGroupId());
        $this->assertTrue($permission->getEditRestriction("admins"));
        $this->assertTrue($permission->getEditRestriction("group"));
        $this->assertTrue($permission->getEditRestriction("owner"));
        $this->assertFalse($permission->getEditRestriction("others"));
        $permission->delete();
        $this->assertEquals(0, Database::getNumRows("select id from `{prefix}enttiy_permissions where name = 'foo'`"));
    }
}