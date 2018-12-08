<?php
use UliCMS\Security\PermissionChecker;

class PermissionCheckerTest extends \PHPUnit\Framework\TestCase
{

    private $testUser;

    private $testGroup1;

    private $testGroup2;

    private $testGroup3;

    public function setUp()
    {
        $group1 = new Group();
        $group1->setName("TestGroup1");
        $group1->addPermission("info", true);
        $group1->save();
        $this->testGroup1 = $group1;
        
        $group2 = new Group();
        $group2->setName("TestGroup2");
        $group2->addPermission("pages", true);
        $group2->addPermission("design", true);
        $group2->save();
        $this->testGroup2 = $group2;
        
        $group3 = new Group();
        $group3->setName("TestGroup3");
        $group3->addPermission("images", true);
        
        $lang = new Language();
        $lang->loadByLanguageCode("en");
        
        $group3->setLanguages(array(
            $lang
        ));
        
        $group3->save();
        $this->testGroup3 = $group3;
        
        $user = new User();
        $user->setUsername("max_muster");
        $user->setFirstname("Max");
        $user->setLastname("Muster");
        $user->setPassword("password123");
        $user->setEmail("max@muster.de");
        $user->setHomepage("http://www.google.de");
        $user->setSkypeId("deruliimnetz");
        $user->setDefaultLanguage("fr");
        $user->setHTMLEditor("ckeditor");
        $user->setTwitter("ulicms");
        $user->setAboutMe("hello world");
        $lastLogin = time();
        $user->setLastLogin($lastLogin);
        $user->setGroup($this->testGroup1);
        $user->addSecondaryGroup($this->testGroup2);
        $user->addSecondaryGroup($this->testGroup3);
        $user->save();
        $this->testUser = $user;
    }

    public function tearDown()
    {
        $this->testUser->delete();
        $this->testGroup1->delete();
        $this->testGroup2->delete();
        $this->testGroup3->delete();
    }

    public function testConstructorWithUserId()
    {
        $checker = new PermissionChecker(123);
        $this->assertEquals(123, $checker->getUserId());
    }

    public function testSetUserId()
    {
        $checker = new PermissionChecker();
        $this->assertNull($checker->getUserId());
        $checker->setUserId(666);
        $this->assertEquals(666, $checker->getUserId());
        $checker->setUserId(null);
        $this->assertNull($checker->getUserId());
    }

    public function testHasPermissionWithUserReturnsTrue()
    {
        $permissionChecker = new PermissionChecker($this->testUser->getId());
        $this->assertTrue($permissionChecker->hasPermission("info"));
        $this->assertTrue($permissionChecker->hasPermission("pages"));
        $this->assertTrue($permissionChecker->hasPermission("images"));
        $this->assertTrue($permissionChecker->hasPermission("design"));
    }

    public function testHasPermissionWithUserReturnsFalse()
    {
        $permissionChecker = new PermissionChecker($this->testUser->getId());
        $this->assertFalse($permissionChecker->hasPermission("settings_simple"));
        $this->assertFalse($permissionChecker->hasPermission("other"));
        $this->assertFalse($permissionChecker->hasPermission("audio"));
        $this->assertFalse($permissionChecker->hasPermission("non_eixsting_permission"));
    }

    public function testGetLanguages()
    {
        $permissionChecker = new PermissionChecker($this->testUser->getId());
        $languages = $permissionChecker->getLanguages();
        $language = $languages[0];
        
        $this->assertEquals(1, count($languages));
        $this->assertEquals("en", $language->getLanguageCode());
    }

    public function testHasPermissionWithoutUser()
    {
        $checker = new PermissionChecker(null);
        $this->assertFalse($checker->hasPermission("info"));
    }

    public function testHasPermissionWithNonExistingUser()
    {
        $checker = new PermissionChecker(PHP_INT_MAX);
        $this->assertFalse($checker->hasPermission("info"));
    }
}