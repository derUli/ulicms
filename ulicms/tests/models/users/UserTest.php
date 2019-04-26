<?php

class UserTest extends \PHPUnit\Framework\TestCase {

    private $otherGroup;

    public function setUp() {
        require_once getLanguageFilePath("en");

        $user = new User();
        $user->loadByUsername("max_muster");
        if (!is_null($user->getId())) {
            $user->delete();
        }
        $group = new Group();
        $group->setName("Other Group");
        $group->save();
        $this->otherGroup = $group;
    }

    public function tearDown() {
        $this->setUp();
        Database::pQuery("delete from `{prefix}groups` where name = ?", array(
            "Other Group"
                ), true);
    }

    public function testCreateAndDeleteUser() {
        $user = new User();
        $user->setUsername("max_muster");
        $user->setFirstname("Max");
        $user->setLastname("Muster");
        $user->setGroupId(1);
        $user->setPassword("password123");
        $user->setEmail("max@muster.de");
        $user->setHomepage("http://www.google.de");
        $user->setDefaultLanguage("fr");
        $user->setHTMLEditor("ckeditor");

        $user->setAboutMe("hello world");
        $lastLogin = time();
        $user->setLastLogin($lastLogin);
        $user->save();
        $this->assertNotNull($user->getId());
        $user = new User();
        $user->loadByUsername("max_muster");
        $this->assertEquals("max_muster", $user->getUsername());
        $this->assertEquals("Max", $user->getFirstname());
        $this->assertEquals("fr", $user->getDefaultLanguage());
        $this->assertEquals("Muster", $user->getLastname());
        $this->assertEquals("max@muster.de", $user->getEmail());
        $this->assertEquals(1, $user->getGroupId());
        $this->assertEquals(1, $user->getGroup()
                        ->getId());
        $this->assertEquals("Administrator", $user->getGroup()
                        ->getName());
        $this->assertEquals(Encryption::hashPassword("password123"), $user->getPassword());
        $this->assertEquals($lastLogin, $user->getLastLogin());
        $this->assertEquals("http://www.google.de", $user->getHomepage());
        $this->assertEquals("ckeditor", $user->getHTMLEditor());
        $this->assertEquals(false, $user->getRequirePasswordChange());
        $this->assertEquals(false, $user->getAdmin());
        $this->assertEquals(false, $user->getLocked());
        $this->assertEquals("hello world", $user->getAboutMe());
        $user->setHTMLEditor("codemirror");
        $user->setRequirePasswordChange(true);
        $user->setLocked(true);
        $user->setAdmin(true);
        $user->setAboutMe("bye");
        $user->setGroup($this->otherGroup);
        $this->assertEquals("Other Group", $user->getGroup()
                        ->getName());
        $this->assertEquals($user->getGroupId(), $user->getGroup()
                        ->getId());
        $user->save();

        $user = new User();
        $user->loadByUsername("max_muster");
        $this->assertEquals("codemirror", $user->getHTMLEditor());

        $this->assertEquals(true, $user->getLocked());
        $this->assertEquals(true, $user->getAdmin());
        $this->assertEquals(true, $user->getRequirePasswordChange());
        $this->assertEquals("bye", $user->getAboutMe());

        // This always returns the URL of an placeholder image
        // since the new avatar feature is not implemented yet
        $this->assertTrue(endsWith($user->getAvatar(), "/admin/gfx/no_avatar.png"));

        $user->delete();

        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testGetWelcomeMailText() {
        $user = new User();
        $user->setUsername("john.doe");
        $user->setLastname("Doe");
        $user->setFirstname("John");
        $user->setPassword("secret");

        $message = $user->getWelcomeMailText("secret");
        $this->assertStringContainsString("An administrator created a user account for you", $message);
        $this->assertStringContainsString("Hello John", $message);
        $this->assertStringContainsString("Username: john.doe", $message);
        $this->assertStringContainsString("Password: secret", $message);
    }

    public function testLoadByEmail() {
        $user = new User();
        $user->setUsername("john-doe");
        $user->setFirstname("John");
        $user->setLastname("Doe");
        $user->setGroupId(1);
        $user->setPassword("password123");
        $user->setEmail("john@doe.com");
        $user->save();

        $loadedUser = new User();
        $loadedUser->loadByEmail("john@doe.com");

        $this->assertEquals("john@doe.com", $loadedUser->getEmail());
        $this->assertEquals("john-doe", $loadedUser->getUsername());

        $user->delete();
    }

}
