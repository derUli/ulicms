<?php

use UliCMS\Security\Encryption;
use UliCMS\Exceptions\NotImplementedException;

class UserTest extends \PHPUnit\Framework\TestCase {

    private $otherGroup;

    public function setUp() {

        $_SERVER["REQUEST_URI"] = "/other-url.html?param=value";

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
        Database::pQuery("delete from `{prefix}groups` where name like ? or name like ?", array(
            "Other Grou%",
            "Main Group"
                ), true);
        unset($_SERVER["REQUEST_URI"]);
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

    public function testLoadByUsernameCaseInsensitive() {
        $user = new User();
        $user->setUsername("paul.panzer");
        $user->setLastname("Panzer");
        $user->setFirstname("Paul");
        $user->setPassword("secret");
        $user->setEmail("paul@panzer.de");
        $user->save();

        $savedUser = new User();
        $savedUser->loadByUsername("Paul.Panzer");

        $this->assertEquals("paul.panzer", $savedUser->getUsername());
        $this->assertEquals("Panzer", $savedUser->getLastname());

        $user->delete();
    }

    public function testLoadByEmailCaseInsensitive() {
        $user = new User();
        $user->setUsername("paul.panzer");
        $user->setLastname("Panzer");
        $user->setFirstname("Paul");
        $user->setPassword("secret");
        $user->setEmail("paul@panzer.de");
        $user->save();

        $savedUser = new User();
        $savedUser->loadByEmail("Paul@PaNzER.DE");

        $this->assertEquals("paul@panzer.de", $savedUser->getEmail());
        $this->assertEquals("Panzer", $savedUser->getLastname());

        $user->delete();
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

    public function testCheckPasswordReturnsTrue() {
        $user = new User();
        $user->setPassword("topsecretpassword");

        $this->assertTrue($user->checkPassword("topsecretpassword"));

        $user->delete();
    }

    public function testCheckPasswordReturnsFalse() {
        $user = new User();
        $user->setPassword("topsecretpassword");
        $this->assertFalse($user->checkPassword("falschesPassW0rt"));

        $user->delete();
    }

    public function testFromSessionDataWithInvalidIdReturnsEmptyUser() {

        @session_start();

        $_SESSION["login_id"] = PHP_INT_MAX;
        $userFromSession = User::fromSessionData();
        $this->assertInstanceOf(User::class, $userFromSession);
        $this->assertNull($userFromSession->getId());
        $this->assertNull($userFromSession->getUsername());

        @session_destroy();
    }

    public function testFromSessionDataWithoutSessionReturnsNull() {

        $userFromSession = User::fromSessionData();
        $this->assertNull($userFromSession);
    }

    public function testFromSessionDataReturnsUser() {
        $manager = new UserManager();
        $users = $manager->getLockedUsers(false);
        $user = $users[0];
        @session_start();

        $_SESSION["login_id"] = $user->getId();

        $userFromSession = User::fromSessionData();

        $this->assertInstanceOf(User::class, $userFromSession);
        $this->assertEquals($userFromSession->getId(), $user->getId());
        $this->assertEquals($userFromSession->getUsername(), $user->getUsername());
        $this->assertEquals($userFromSession->getLastname(), $user->getLastname());

        @session_destroy();
    }

    public function testRegisterSessionRegistersSession() {
        $manager = new UserManager();
        $users = $manager->getLockedUsers(false);
        $user = $users[0];

        $user->registerSession();

        $this->assertEquals(get_user_id(), $user->getId());
        $this->assertEquals($user->getUsername(), $_SESSION["ulicms_login"]);
        $this->assertNotNull(session_id());
        @session_destroy();
    }

    public function testRegisterSessionThrowError() {
        $this->expectException(BadMethodCallException::class);
        $user = new User();
        $user->registerSession();
    }

    public function testToSessionDataReturnsNull() {
        $user = new User();
        $this->assertNull($user->toSessionData());
    }

    public function testToSessionDataReturnsArray() {
        $manager = new UserManager();
        $users = $manager->getLockedUsers(false);
        $user = $users[0];
        $sessionData = $user->toSessionData();
        $this->assertIsArray($sessionData);
        $this->assertCount(9, $sessionData);
        $this->assertEquals($user->getId(), $sessionData["login_id"]);
        $this->assertEquals($user->getUsername(), $sessionData["ulicms_login"]);

        $this->assertEquals($user->getLastname(), $sessionData["lastname"]);
    }

    public function testGetAllGroupsReturnsEmptyArray() {
        $user = new User();
        $user->setUsername("john-doe");
        $user->setFirstname("John");
        $user->setLastname("Doe");
        $user->setPassword("password123");
        $user->setEmail("john@doe.com");
        $user->save();

        $this->assertCount(0, $user->getAllGroups());

        $user->delete();
    }

    public function testGetAllGroupsReturnsGroups() {

        $user = new User();
        $user->setUsername("john-doe");
        $user->setFirstname("John");
        $user->setLastname("Doe");
        $user->setPassword("password123");
        $user->setEmail("john@doe.com");

        $group1 = new Group();
        $group1->setName("Main Group");
        $group1->save();

        $group2 = new Group();
        $group2->setName("Other Group 1");
        $group2->save();

        $group3 = new Group();
        $group3->setName("Other Group 2");
        $group3->save();

        $user->setPrimaryGroup($group1);
        $user->setSecondaryGroups([$group2, $group3]);
        $user->save();

        $allGroups = $user->getAllGroups();
        $this->assertCount(3, $allGroups);

        $this->assertEquals($allGroups[0]->getName(),
                "Main Group"
        );
        $this->assertEquals($allGroups[1]->getName(),
                "Other Group 1"
        );
        $this->assertEquals($allGroups[2]->getName(),
                "Other Group 2"
        );

        $user->delete();
    }

    public function testGetPasswordChanged() {
        $user = new User();
        $user->setPassword("top-secret");
        $this->assertRegExp(
                '/\d{1,4}-\d{1,2}-\d[1,2] \d{1,2}:\d{1,2}:\d{1,2}/',
                $user->getPasswordChanged()
        );
    }

    public function testSetAndGetLastAction() {
        $user = new User();
        $user->setUsername("john-doe");
        $user->setFirstname("John");
        $user->setLastname("Doe");
        $user->setPassword("password123");
        $user->setEmail("john@doe.com");
        // does nothing because the dataset is not saved yet
        $user->setLastAction(null);
        $user->save();

        // is zero until the user does something
        $this->assertEquals(0, $user->getLastAction());

        $time = time();
        $user->setLastAction($time);
        $this->assertEquals($time, $user->getLastAction());

        $user->setLastAction(null);
        $this->assertEquals(0, $user->getLastAction());

        $user->delete();
    }

    // Avatar is not implemented yet
    // UliCMS had support for Gravatars
    // but had to dropped it because
    // the introduction of the new GDPR privacy laws in
    // the european union
    public function testSetAvatar() {

        $user = new User();
        $this->expectException(NotImplementedException::class);
        $user->setAvatar($file);
    }

    public function testRemoveSecondaryGroup() {

        $group1 = new Group();
        $group1->setName("Group1");
        $group1->setId(123);

        $group2 = new Group();
        $group2->setName("Group2");
        $group2->setId(456);

        $user = new User();
        $user->setSecondaryGroups([$group1, $group2]);

        $this->assertCount(2, $user->getSecondaryGroups());

        $user->removeSecondaryGroup($group1);

        $this->assertCount(1, $user->getSecondaryGroups());
        $this->assertEquals(
                "Group2",
                $user->getSecondaryGroups()[0]->getName()
        );
    }

    public function testSetHtmlEditorToNonSupported() {
        $user = new User();

        $user->setHTMLEditor("codemirror");
        $this->assertEquals("codemirror", $user->getHTMLEditor());

        // there is no "super_editor" so UliCMS sets html_editor
        // to the default value
        $user->setHTMLEditor("super_editor");
        $this->assertEquals("ckeditor", $user->getHTMLEditor());
    }

}
