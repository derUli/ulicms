<?php

class EntityPermissionsTest extends PHPUnit_Framework_TestCase
{

    private $testUser1;

    private $testUser2;

    private $testGroup1;

    private $testGroup2;

    public function setUp()
    {}

    public function testCreateEditAndDeletePermission()
    {
        $permission = new EntityPermissions("foo", 123);
        $this->assertEquals(123, $permission->getEntityId());
        $this->assertEquals("foo", $permission->getEntityName());
        // ...
    }
}