<?php

use UliCMS\Security\Permissions\PagePermissions;

class PagePermissionsTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        $sql = "delete from `{prefix}content` where slug = ?";
        $args = array(
            "page_permission_test"
        );
        Database::pQuery($sql, $args, true);
    }

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

    public function testCreatePageWithPermissions()
    {
        $page = new Page();
        $page->slug = "page_permission_test";
        $page->title = "Page Permission Test";
        $page->language = "en";

        $manager = new UserManager();
        $users = $manager->getAllUsers();
        $firstUser = $users[0];

        $page->author_id = $firstUser->getId();
        $groups = Group::getAll();
        $firstGroup = $groups[0];
        $page->group_id = $firstGroup->getId();

        $page->getPermissions()->setEditRestriction("owner", true);
        $page->getPermissions()->setEditRestriction("group", true);

        $this->assertTrue($page->getPermissions()
                        ->getEditRestriction("owner"));
        $this->assertTrue($page->getPermissions()
                        ->getEditRestriction("group"));
        $this->assertFalse($page->getPermissions()
                        ->getEditRestriction("others"));
        $this->assertFalse($page->getPermissions()
                        ->getEditRestriction("admins"));
        $page->save();

        $page2 = new Page();
        $page2->loadBySlugAndLanguage("page_permission_test", "en");
        $this->assertEquals("Page Permission Test", $page2->title);
        $this->assertTrue($page->getPermissions()
                        ->getEditRestriction("owner"));
        $this->assertTrue($page->getPermissions()
                        ->getEditRestriction("group"));
        $this->assertFalse($page->getPermissions()
                        ->getEditRestriction("others"));
        $this->assertFalse($page->getPermissions()
                        ->getEditRestriction("admins"));

        $page2->delete();
    }

    public function testSetAndGetEditRestrictionForNonExistingObject()
    {
        $page = new Page();

        // Der Pumuckl ist nur das Hirngespinst von Meister Eder
        $page->getPermissions()->setEditRestriction("pumuckl", true);
        $this->assertNull($page->getPermissions()->getEditRestriction("pumuckl"));
    }
}
