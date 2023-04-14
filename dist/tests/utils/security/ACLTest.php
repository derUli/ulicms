<?php

use App\Constants\HtmlEditor;

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
        $acl = new \App\Security\ACL();
        $groups = $acl->getAllGroups();
        $this->assertGreaterThanOrEqual(1, count($groups));
        foreach ($groups as $id => $name) {
            $this->assertIsNumeric($id);
            $this->assertNotEmpty($name);
        }
    }

    public function testGetDefaultACLAsJSONWithAdminAndPlain()
    {
        $acl = new \App\Security\ACL();
        $output = $acl->getDefaultACLAsJSON(true, false);

        $this->assertTrue(is_json($output));
    }

    public function testGetDefaultACLWithAdminAndPlain()
    {
        $acl = new \App\Security\ACL();
        $output = $acl->getDefaultACL(true, false);

        $this->assertTrue(is_json($output));
    }

    public function testCreateUpdateAndDeleteGroup()
    {
        $acl = new \App\Security\ACL();
        $id = $acl->createGroup(
            'Test-Gruppe ' . uniqid(),
            [
                'foo' => true
            ]
        );
        $this->assertGreaterThan(0, $id);

        $this->assertEquals(
            $id,
            $acl->updateGroup(
                $id,
                'Test-Gruppe ' . uniqid() . ' umbenannt',
                [
                    'hello' => false
                ]
            )
        );
        $acl->deleteGroup($id);
    }

    public function testDeleteGroupMoveToOthers()
    {
        $acl = new \App\Security\ACL();
        $id = $acl->createGroup(
            'Test-Gruppe ' . uniqid(),
            [
                'foo' => true
            ]
        );

        $otherGroupId = $acl->createGroup(
            'Test-Gruppe 2 ' . uniqid(),
            [
                'foo' => true
            ]
        );

        $user = new User();
        $user->setUsername('testuser-1');
        $user->setPassword(rand_string(23));
        $user->setLastname('Beutlin');
        $user->setFirstname('Bilbo');
        $user->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user->setPrimaryGroupId($id);
        $user->save();

        $acl = new \App\Security\ACL();

        $acl->deleteGroup($id, $otherGroupId);

        $user->loadById($user->getId());

        $this->assertEquals($otherGroupId, $user->getPrimaryGroupId());

        $acl->deleteGroup($otherGroupId);

        $user->loadById($user->getId());
        $this->assertNull($user->getPrimaryGroupId());
    }

    public function testPermissionQueryResultWithNonExistingGroupReturnsNull()
    {
        $acl = new \App\Security\ACL();
        $this->assertNull($acl->getPermissionQueryResult(PHP_INT_MAX));
    }

    public function testPermissionQueryResultWithoutIdReturnsNull()
    {
        $acl = new \App\Security\ACL();
        $this->assertNull($acl->getPermissionQueryResult());
    }

    public function testPermissionQueryResultWithGroupIdAsArgumentReturnsAssoc()
    {
        $group = new Group();
        $group->setName('testgroup');
        $group->save();
        $acl = new \App\Security\ACL();

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

        $acl = new \App\Security\ACL();
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
        $acl = new \App\Security\ACL();
        $this->assertFalse($acl->hasPermission('foobar'));
    }
}
