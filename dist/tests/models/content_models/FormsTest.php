<?php

class FormsTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::query("delete from {prefix}forms where name like 'Unit Test%'", true);
    }

    public function testCreateAndDeleteWithEnabled(): void {
        $pages = ContentFactory::getAllRegular();
        $page = $pages[0];
        Forms::createForm(
            'Unit Test 1',
            'max@muster.de',
            'Subject 1',
            1,
            'message=>Message',
            'message',
            'email',
            (int)$page->id,
            true
        );
        $id = Database::getLastInsertID();
        $form = Forms::getFormByID($id);
        $this->assertEquals($id, $form['id']);
        $this->assertEquals('Unit Test 1', $form['name']);
        $this->assertEquals(1, $form['enabled']);
        $this->assertEquals('max@muster.de', $form['email_to']);
        $this->assertEquals('Subject 1', $form['subject']);
        $this->assertEquals(1, $form['category_id']);
        $this->assertEquals('message=>Message', $form['fields']);
        $this->assertEquals('message', $form['required_fields']);
        $this->assertEquals('email', $form['mail_from_field']);
        $this->assertEquals($page->id, $form['target_page_id']);
        $this->assertGreaterThan(time() - 100, $form['created']);
        $this->assertGreaterThan(time() - 100, $form['updated']);

        Forms::deleteForm($id);

        $form = Forms::getFormByID($id);
        $this->assertNull($form);
    }

    public function testCreateAndDeleteWithDisabled(): void {
        $pages = ContentFactory::getAllRegular();
        $page = $pages[0];
        Forms::createForm(
            'Unit Test 2',
            'max@muster.de',
            'Subject 1',
            1,
            'message=>Message',
            'message',
            'email',
            (int)$page->id,
            false
        );
        $id = Database::getLastInsertID();
        $form = Forms::getFormByID($id);
        $this->assertEquals($id, $form['id']);
        $this->assertEquals('Unit Test 2', $form['name']);
        $this->assertEquals(0, $form['enabled']);
        $this->assertEquals('max@muster.de', $form['email_to']);
        $this->assertEquals('Subject 1', $form['subject']);
        $this->assertEquals(1, $form['category_id']);
        $this->assertEquals('message=>Message', $form['fields']);
        $this->assertEquals('message', $form['required_fields']);
        $this->assertEquals('email', $form['mail_from_field']);
        $this->assertEquals($page->id, $form['target_page_id']);
        $this->assertGreaterThan(time() - 100, $form['created']);
        $this->assertGreaterThan(time() - 100, $form['updated']);

        Forms::deleteForm($id);

        $form = Forms::getFormByID($id);
        $this->assertNull($form);
    }

    public function testEditAndDeleteWithEnabled(): void {
        $pages = ContentFactory::getAllRegular();
        $page1 = $pages[0];
        $page2 = array_pop($pages);

        Forms::createForm(
            'Unit Test 2',
            'max@muster.de',
            'Subject 1',
            1,
            'message=>Message',
            'message',
            'email',
            (int)$page1->id,
            false
        );
        $id = Database::getLastInsertID();

        Forms::editForm(
            $id,
            'Unit Test 3',
            'foo@bar.de',
            'My Subject',
            1,
            'name=>Name',
            'name',
            'mail_from',
            $page2->id,
            true
        );

        $form = Forms::getFormByID($id);

        $this->assertEquals($id, $form['id']);
        $this->assertEquals('Unit Test 3', $form['name']);
        $this->assertEquals(1, $form['enabled']);
        $this->assertEquals('foo@bar.de', $form['email_to']);
        $this->assertEquals('My Subject', $form['subject']);
        $this->assertEquals(1, $form['category_id']);
        $this->assertEquals('name=>Name', $form['fields']);
        $this->assertEquals('name', $form['required_fields']);
        $this->assertEquals('mail_from', $form['mail_from_field']);
        $this->assertEquals($page2->id, $form['target_page_id']);

        $this->assertGreaterThan(time() - 100, $form['created']);
        $this->assertGreaterThan(time() - 100, $form['updated']);

        Forms::deleteForm($id);
    }

    public function testEditAndDeleteWithDisabled(): void {
        $pages = ContentFactory::getAllRegular();
        $page1 = $pages[0];
        $page2 = array_pop($pages);
        Forms::createForm(
            'Unit Test 2',
            'max@muster.de',
            'Subject 1',
            1,
            'message=>Message',
            'message',
            'email',
            (int)$page1->id,
            true
        );
        $id = Database::getLastInsertID();

        Forms::editForm(
            $id,
            'Unit Test 3',
            'foo@bar.de',
            'My Subject',
            1,
            'name=>Name',
            'name',
            'mail_from',
            $page2->id,
            false
        );

        $form = Forms::getFormByID($id);

        $this->assertEquals($id, $form['id']);
        $this->assertEquals('Unit Test 3', $form['name']);
        $this->assertEquals(0, $form['enabled']);
        $this->assertEquals('foo@bar.de', $form['email_to']);
        $this->assertEquals('My Subject', $form['subject']);
        $this->assertEquals(1, $form['category_id']);
        $this->assertEquals('name=>Name', $form['fields']);
        $this->assertEquals('name', $form['required_fields']);
        $this->assertEquals('mail_from', $form['mail_from_field']);
        $this->assertEquals($page2->id, $form['target_page_id']);

        $this->assertGreaterThan(time() - 100, $form['created']);
        $this->assertGreaterThan(time() - 100, $form['updated']);

        Forms::deleteForm($id);
    }

    public function testGetAllForms(): void {
        $pages = ContentFactory::getAllRegular();
        $page1 = $pages[0];

        Forms::createForm(
            'Unit Test 1',
            'max@muster.de',
            'Subject 1',
            1,
            'message=>Message',
            'message',
            'email',
            (int)$page1->id,
            false
        );
        $id1 = Database::getLastInsertID();
        Forms::createForm(
            'Unit Test 2',
            'max@muster.de',
            'Subject 1',
            1,
            'message=>Message',
            'message',
            'email',
            (int)$page1->id,
            false
        );

        $id2 = Database::getLastInsertID();

        $forms = Forms::getAllForms();
        $this->assertGreaterThanOrEqual(2, count($forms));
        foreach ($forms as $form) {
            $this->assertIsArray($form);
            $this->assertGreaterThanOrEqual(1, $form['id']);
            $this->assertNotEmpty($form['name']);
            $this->assertNotEmpty($form['name']);
            $this->assertNotEmpty($form['name']);
            $this->assertNotEmpty($form['name']);
            $this->assertNotEmpty($form['name']);
            $this->assertNotEmpty($form['name']);
        }
        Forms::deleteForm($id1);
        Forms::deleteForm($id2);
    }
}
