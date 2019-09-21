<?php

class ACLTest extends \PHPUnit\Framework\TestCase {

    public function testGetAllGroups() {
        $acl = new ACL();
        $groups = $acl->getAllGroups();
        $this->assertGreaterThanOrEqual(1, count($groups));
        foreach ($groups as $id => $name) {
            $this->assertIsNumeric($id);
            $this->assertNotEmpty($name);
        }
    }

    public function testGetDefaultACLAsJSONWithAdminAndPlain() {
        $acl = new ACL();
        $expected = file_get_contents(
                Path::resolve("ULICMS_ROOT/tests/fixtures/json/default_acl.json"));

        $output = $acl->getDefaultACLAsJSON(true, false);
        $this->assertEquals($expected, $output);
    }

    public function testGetDefaultACLWithAdminAndPlain() {
        $acl = new ACL();
        $expected = file_get_contents(
                Path::resolve("ULICMS_ROOT/tests/fixtures/json/default_acl.json"));

        $output = $acl->getDefaultACL(true, false);
        $this->assertEquals($expected, $output);
    }

    public function testCreateUpdateAndDeleteGroup() {
        $acl = new ACL();
        $id = $acl->createGroup(
                "Test-Gruppe " . uniqid(),
                [
                    "foo" => true
                ]
        );
        $this->assertGreaterThan(0, $id);

        $this->assertEquals(
                $id,
                $acl->updateGroup(
                        $id,
                        "Test-Gruppe " . uniqid() . " umbenannt",
                        [
                            "hello" => false
                        ]
                )
        );
        $acl->deleteGroup($id);
    }

}
