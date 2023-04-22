<?php

use App\Security\Permissions\ACL;

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

    public function testGetDefaultACL()
    {
        $actual = ACL::getDefaultACL(false);

        $this->assertIsArray($actual);

        foreach($actual as $key => $value){
            $this->assertIsString($key);
            $this->assertFalse($value);
        }
    }

    public function testGetDefaultACLAdmin()
    {
        $actual = ACL::getDefaultACL(true);

        $this->assertIsArray($actual);

        foreach($actual as $key => $value){
            $this->assertIsString($key);
            $this->assertTrue($value);
        }
    }

    public function testPermissionQueryResultWithNonExistingGroupReturnsNull()
    {
        $this->assertNull(ACL::getPermissionQueryResult(PHP_INT_MAX));
    }

    public function testPermissionQueryResultWithoutIdReturnsNull()
    {
        $this->assertNull(ACL::getPermissionQueryResult());
    }

    public function testPermissionQueryResultWithGroupIdAsArgumentReturnsAssoc()
    {
        $group = new Group();
        $group->setName('testgroup');
        $group->save();

        $groupData = ACL::getPermissionQueryResult($group->getId());

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

        $groupData = ACL::getPermissionQueryResult();
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
}
