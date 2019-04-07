<?php

use UliCMS\Security\ContentPermissionChecker;

class ContentPermissionCheckerTest extends \PHPUnit\Framework\TestCase {

    private $testUser1;
    private $testUser2;
    private $testGroup1;
    private $testGroup2;

    public function setUp() {
        $this->testGroup1 = new Group();
        $this->testGroup1->setName("testgroup1");
        $this->testGroup1->addPermission("pages", true);
        $this->testGroup1->addPermission("pages_activate_others", true);
        $this->testGroup1->addPermission("pages_activate_own", true);
        $this->testGroup1->addPermission("pages_change_owner", true);
        $this->testGroup1->addPermission("pages_create", true);
        $this->testGroup1->addPermission("pages_edit_others", true);
        $this->testGroup1->addPermission("pages_edit_own", true);
        $this->testGroup1->addPermission("pages_edit_permissions", true);
        $this->testGroup1->addPermission("pages_show_positions", true);
        $this->testGroup1->save();

        $this->testGroup2 = new Group();
        $this->testGroup2->setName("testgroup2");
        $this->testGroup2->addPermission("pages", true);
        $this->testGroup2->addPermission("pages_activate_others", true);
        $this->testGroup2->addPermission("pages_activate_own", true);
        $this->testGroup2->addPermission("pages_change_owner", true);
        $this->testGroup2->addPermission("pages_create", true);
        $this->testGroup2->addPermission("pages_edit_others", true);
        $this->testGroup2->addPermission("pages_edit_own", true);
        $this->testGroup2->addPermission("pages_edit_permissions", true);
        $this->testGroup2->addPermission("pages_show_positions", true);
        $this->testGroup2->save();

        $this->testGroup3 = new Group();
        $this->testGroup3->setName("testgroup3");
        $this->testGroup3->save();

        $this->testUser1 = new User();
        $this->testUser1->setUsername("testuser1");
        $this->testUser1->setLastname("Doe");
        $this->testUser1->setFirstname("John");
        $this->testUser1->setPassword("foobar");
        $this->testUser1->setGroup($this->testGroup3);
        $this->testUser1->addSecondaryGroup($this->testGroup1);
        $this->testUser1->save();

        $this->testUser2 = new User();
        $this->testUser2->setUsername("testuser2");
        $this->testUser2->setLastname("Doe");
        $this->testUser2->setFirstname("Jane");
        $this->testUser2->setPassword("foobar");
        $this->testUser2->setGroup($this->testGroup1);
        $this->testUser2->save();
    }

    public function tearDown() {
        Database::query("delete from `{prefix}content` where systemname like 'testpage%'", true);

        Database::query("delete from `{prefix}users` where username like 'testuser%'", true);
        Database::query("delete from `{prefix}groups` where name like 'testgroup%'", true);
    }

    // TODO: we need test cases for any combination of edit restrictions and user and group permissions
    // maybe write a permission matrix table for reference?
    // TODO: Write more test cases for canRead()
    public function testCanReadReturnsTrue() {
        $pages = ContentFactory::getAll();
        $checker = new ContentPermissionChecker($this->testUser1->getId());
        foreach ($pages as $page) {
            $this->assertTrue($checker->canRead($page->getID()));
        }
    }

    // No edit restrictions
    public function testCanWriteWithNoEditRestrictionsReturnsTrue() {
        $checker = new ContentPermissionChecker($this->testUser1->getId());

        $page = new Page();
        $page->systemname = "testpage1";
        $page->language = "de";
        $page->group_id = $this->testGroup1->getId();
        $page->autor = $this->testUser2->getId();

        $page->save();

        $this->assertTrue($checker->canWrite($page->getID()));

        $page->delete();
    }

    public function testCanWriteWithEditRestrictionsReturnsTrue() {
        $checker = new ContentPermissionChecker($this->testUser1->getId());

        $page = new Page();
        $page->systemname = "testpage2";
        $page->language = "de";
        $page->group_id = $this->testGroup2->getId();
        $page->autor = $this->testUser1->getId();
        $page->getPermissions()->setEditRestriction("group", true);
        $page->getPermissions()->setEditRestriction("owner", true);
        $page->save();

        $this->assertTrue($checker->canWrite($page->getID()));

        $page->delete();
    }

    // content has edit restrictions, we can edit the content
    public function testCanWriteWithEditRestrictionsReturnsFalse() {
        $checker = new ContentPermissionChecker($this->testUser1->getId());

        $page = new Page();
        $page->systemname = "testpage3";
        $page->language = "de";
        $page->group_id = $this->testGroup2->getId();
        $page->autor = $this->testUser2->getId();
        $page->getPermissions()->setEditRestriction("group", true);
        $page->getPermissions()->setEditRestriction("owner", true);
        $page->save();
        $this->assertFalse($checker->canWrite($page->getID()));

        $page->delete();
    }

    public function testCanDeleteWithEditRestrictionsReturnsTrue() {
        $checker = new ContentPermissionChecker($this->testUser1->getId());

        $page = new Page();
        $page->systemname = "testpage2";
        $page->language = "de";
        $page->group_id = $this->testGroup2->getId();
        $page->autor = $this->testUser1->getId();
        $page->getPermissions()->setEditRestriction("group", true);
        $page->getPermissions()->setEditRestriction("owner", true);
        $page->save();

        $this->assertTrue($checker->canDelete($page->getID()));

        $page->delete();
    }

    // content has edit restrictions, we can edit the content
    public function testCanDeleteWithEditRestrictionsReturnsFalse() {
        $checker = new ContentPermissionChecker($this->testUser1->getId());

        $page = new Page();
        $page->systemname = "testpage3";
        $page->language = "de";
        $page->group_id = $this->testGroup2->getId();
        $page->autor = $this->testUser2->getId();
        $page->getPermissions()->setEditRestriction("group", true);
        $page->getPermissions()->setEditRestriction("owner", true);
        $page->save();
        $this->assertFalse($checker->canDelete($page->getID()));

        $page->delete();
    }

}
