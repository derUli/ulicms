<?php

class PagePermissionsTest extends PHPUnit_Framework_TestCase
{

    public function testPagePermissionsConstructorDefault()
    {
        $permissions = new PagePermissions();
        $this->assertFalse($permissions->getEditRestriction("admins"));
        $this->assertFalse($permissions->getEditRestriction("group"));
        $this->assertFalse($permissions->getEditRestriction("owner"));
        $this->assertFalse($permissions->getEditRestriction("others"));
    }

    public function testPagePermissionsConstructorWithArguments()
    {
        $permissions = new PagePermissions(array(
            "group" => true,
            "others" => false,
            "owner" => true
        ));
        $this->assertFalse($permissions->getEditRestriction("admins"));
        $this->assertTrue($permissions->getEditRestriction("group"));
        $this->assertTrue($permissions->getEditRestriction("owner"));
        $this->assertFalse($permissions->getEditRestriction("others"));
    }

    public function testPagePermissionsSetEditRestriction()
    {
        $permissions = new PagePermissions();
        $permissions->setEditRestriction("others", true);
        $this->assertFalse($permissions->getEditRestriction("admins"));
        $this->assertFalse($permissions->getEditRestriction("group"));
        $this->assertFalse($permissions->getEditRestriction("owner"));
        $this->assertTrue($permissions->getEditRestriction("others"));
    }

    public function testPagePermissionsgetAll()
    {
        $permissions = new PagePermissions(array(
            "group" => true,
            "others" => false,
            "owner" => true
        ));
        $all = $permissions->getAll();
        $this->assertEquals(4, count($all));
        $this->assertTrue($all["group"]);
        $this->assertFalse($all["others"]);
    }
}
