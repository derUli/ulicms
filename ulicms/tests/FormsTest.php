<?php
use UliCMS\Exceptions\NotImplementedException;

class FormsTest extends \PHPUnit\Framework\TestCase
{

    public function tearDown()
    {
        Database::query("delete from {prefix}forms where name like 'Unit Test%'", true);
    }

    public function testCreateAndDeleteWithEnabled()
    {
        $pages = ContentFactory::getAllRegular();
        $page = $pages[0];
        Forms::createForm("Unit Test 1", "max@muster.de", "Subject 1", 1, "message=>Message", "message", "email", intval($page->id), true);
        $id = Database::getInsertID();
        $form = Forms::getFormByID($id);
        $this->assertEquals($id, $form["id"]);
        $this->assertEquals("Unit Test 1", $form["name"]);
        $this->assertEquals(1, $form["enabled"]);
        $this->assertEquals("max@muster.de", $form["email_to"]);
        $this->assertEquals("Subject 1", $form["subject"]);
        $this->assertEquals(1, $form["category_id"]);
        $this->assertEquals("message=>Message", $form["fields"]);
        $this->assertEquals("message", $form["required_fields"]);
        $this->assertEquals("email", $form["mail_from_field"]);
        $this->assertEquals($page->id, $form["target_page_id"]);
        $this->assertGreaterThan(time() - 100, $form["created"]);
        $this->assertGreaterThan(time() - 100, $form["updated"]);
        
        Forms::deleteForm($id);
        
        $form = Forms::getFormByID($id);
        $this->assertNull($form);
    }

    public function testCreateAndDeleteWithDisabled()
    {
        $pages = ContentFactory::getAllRegular();
        $page = $pages[0];
        Forms::createForm("Unit Test 2", "max@muster.de", "Subject 1", 1, "message=>Message", "message", "email", intval($page->id), false);
        $id = Database::getInsertID();
        $form = Forms::getFormByID($id);
        $this->assertEquals($id, $form["id"]);
        $this->assertEquals("Unit Test 2", $form["name"]);
        $this->assertEquals(0, $form["enabled"]);
        $this->assertEquals("max@muster.de", $form["email_to"]);
        $this->assertEquals("Subject 1", $form["subject"]);
        $this->assertEquals(1, $form["category_id"]);
        $this->assertEquals("message=>Message", $form["fields"]);
        $this->assertEquals("message", $form["required_fields"]);
        $this->assertEquals("email", $form["mail_from_field"]);
        $this->assertEquals($page->id, $form["target_page_id"]);
        $this->assertGreaterThan(time() - 100, $form["created"]);
        $this->assertGreaterThan(time() - 100, $form["updated"]);
        
        Forms::deleteForm($id);
        
        $form = Forms::getFormByID($id);
        $this->assertNull($form);
    }

    public function testEditAndDeleteWithEnabled()
    {
        $pages = ContentFactory::getAllRegular();
        $page1 = $pages[0];
        $page2 = array_pop($pages);
        
        Forms::createForm("Unit Test 2", "max@muster.de", "Subject 1", 1, "message=>Message", "message", "email", intval($page1->id), false);
        $id = Database::getInsertID();
        
        Forms::editForm($id, "Unit Test 3", "foo@bar.de", "My Subject", 1, "name=>Name", "name", "mail_from", $page2->id, true);
        
        $form = Forms::getFormByID($id);
        
        $this->assertEquals($id, $form["id"]);
        $this->assertEquals("Unit Test 3", $form["name"]);
        $this->assertEquals(1, $form["enabled"]);
        $this->assertEquals("foo@bar.de", $form["email_to"]);
        $this->assertEquals("My Subject", $form["subject"]);
        $this->assertEquals(1, $form["category_id"]);
        $this->assertEquals("name=>Name", $form["fields"]);
        $this->assertEquals("name", $form["required_fields"]);
        $this->assertEquals("mail_from", $form["mail_from_field"]);
        $this->assertEquals($page2->id, $form["target_page_id"]);
        
        $this->assertGreaterThan(time() - 100, $form["created"]);
        $this->assertGreaterThan(time() - 100, $form["updated"]);
        
        Forms::deleteForm($id);
    }

    public function testEditAndDeleteWithDisabled()
    {
        $pages = ContentFactory::getAllRegular();
        $page1 = $pages[0];
        $page2 = array_pop($pages);
        Forms::createForm("Unit Test 2", "max@muster.de", "Subject 1", 1, "message=>Message", "message", "email", intval($page1->id), true);
        $id = Database::getInsertID();
        
        Forms::editForm($id, "Unit Test 3", "foo@bar.de", "My Subject", 1, "name=>Name", "name", "mail_from", $page2->id, false);
        
        $form = Forms::getFormByID($id);
        
        $this->assertEquals($id, $form["id"]);
        $this->assertEquals("Unit Test 3", $form["name"]);
        $this->assertEquals(0, $form["enabled"]);
        $this->assertEquals("foo@bar.de", $form["email_to"]);
        $this->assertEquals("My Subject", $form["subject"]);
        $this->assertEquals(1, $form["category_id"]);
        $this->assertEquals("name=>Name", $form["fields"]);
        $this->assertEquals("name", $form["required_fields"]);
        $this->assertEquals("mail_from", $form["mail_from_field"]);
        $this->assertEquals($page2->id, $form["target_page_id"]);
        
        $this->assertGreaterThan(time() - 100, $form["created"]);
        $this->assertGreaterThan(time() - 100, $form["updated"]);
        
        Forms::deleteForm($id);
    }
}