<?php


class ACLTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testGetAllGroups()
    {
        $acl = new \App\Security\Permissions\ACL();
        $groups = $acl->getAllGroups();
        $this->assertGreaterThanOrEqual(1, count($groups));
        foreach ($groups as $id => $name) {
            $this->assertIsNumeric($id);
            $this->assertNotEmpty($name);
        }
    }

    public function testGetDefaultACL()
    {
        $acl = new \App\Security\Permissions\ACL();
        $actual = $acl->getDefaultACL(false);

        $this->assertIsArray($actual);

        foreach($actual as $key => $value){
            $this->assertIsString($key);
            $this->assertFalse($value);
        }
    }

    public function testGetDefaultACLAdmin()
    {
        $acl = new \App\Security\Permissions\ACL();
        $actual = $acl->getDefaultACL(true);

        $this->assertIsArray($actual);

        foreach($actual as $key => $value){
            $this->assertIsString($key);
            $this->assertTrue($value);
        }
    }

    public function testPermissionQueryResultWithNonExistingGroupReturnsNull()
    {
        $acl = new \App\Security\Permissions\ACL();
        $this->assertNull($acl->getPermissionQueryResult(PHP_INT_MAX));
    }

    public function testPermissionQueryResultWithoutIdReturnsNull()
    {
        $acl = new \App\Security\Permissions\ACL();
        $this->assertNull($acl->getPermissionQueryResult());
    }

    public function testPermissionQueryResultWithGroupIdAsArgumentReturnsAssoc()
    {
        $group = new Group();
        $group->setName('testgroup');
        $group->save();
        $acl = new \App\Security\Permissions\ACL();

        $groupData = $acl->getPermissionQueryResult($group->getId());
        $this->assertIsArray($groupData);

        $this->assertGreaterThanOrEqual(4, count($groupData));
        $this->assertIsNumeric($groupData['id']);
        $this->assertNotEmpty($groupData['name']);
        $this->assertTrue(is_json($groupData['permissions']));
        $this->assertArrayHasKey(
            'allowable_tags',
            $groupData
        );

        $group->delete();
    }

    public function testPermissionQueryResultWithGroupIdFromSessioneturnsAssoc()
    {
        $group = new Group();
        $group->setName('testgroup');
        $group->save();

        $_SESSION['group_id'] = $group->getId();

        $acl = new \App\Security\Permissions\ACL();
        $groupData = $acl->getPermissionQueryResult();
        $this->assertIsArray($groupData);

        $this->assertGreaterThanOrEqual(4, count($groupData));
        $this->assertIsNumeric($groupData['id']);
        $this->assertNotEmpty($groupData['name']);
        $this->assertTrue(is_json($groupData['permissions']));
        $this->assertArrayHasKey(
            'allowable_tags',
            $groupData
        );

        $group->delete();
    }

    public function testHasPermissionReturnsFalse() {
        $acl = new \App\Security\Permissions\ACL();
        $this->assertFalse($acl->hasPermission('foobar'));
    }
}
