<?php

use App\Constants\HtmlEditor;
use App\Models\Content\Language;
use App\Security\Permissions\PermissionChecker;

class PermissionCheckerTest extends \PHPUnit\Framework\TestCase {
    private $testUser;

    private $testGroup1;

    private $testGroup2;

    private $testGroup3;

    protected function setUp(): void {
        $_SESSION = [];
        $group1 = new Group();
        $group1->setName('TestGroup1');
        $group1->addPermission('info', true);
        $group1->save();
        $this->testGroup1 = $group1;

        $group2 = new Group();
        $group2->setName('TestGroup2');
        $group2->addPermission('pages', true);
        $group2->addPermission('design', true);
        $group2->save();
        $this->testGroup2 = $group2;

        $group3 = new Group();
        $group3->setName('TestGroup3');
        $group3->addPermission('files', true);

        $lang = new Language();
        $lang->loadByLanguageCode('en');

        $group3->setLanguages([
            $lang
        ]);

        $group3->save();
        $this->testGroup3 = $group3;

        $user = new User();
        $user->setUsername('max_muster');
        $user->setFirstname('Max');
        $user->setLastname('Muster');
        $user->setPassword('password123');
        $user->setEmail('max@muster.de');
        $user->setHomepage('http://www.google.de');
        $user->setDefaultLanguage('fr');
        $user->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user->setAboutMe('hello world');
        $lastLogin = time();
        $user->setLastLogin($lastLogin);
        $user->setPrimaryGroup($this->testGroup1);
        $user->addSecondaryGroup($this->testGroup2);
        $user->addSecondaryGroup($this->testGroup3);
        $user->save();
        $this->testUser = $user;
    }

    protected function tearDown(): void {
        $this->testUser->delete();
        $this->testGroup1->delete();
        $this->testGroup2->delete();
        $this->testGroup3->delete();
    }

    public function testConstructorWithUserId(): void {
        $checker = new PermissionChecker(123);
        $this->assertEquals(123, $checker->getUserId());
    }

    public function testFromCurrentUser(): void {
        $checker = PermissionChecker::fromCurrentUser();
        $this->assertFalse($checker->hasPermission('foobar'));
    }

    public function testSetUserId(): void {
        $checker = new PermissionChecker();
        $this->assertNull($checker->getUserId());
        $checker->setUserId(666);
        $this->assertEquals(666, $checker->getUserId());
        $checker->setUserId(null);
        $this->assertNull($checker->getUserId());
    }

    public function testHasPermissionWithUserReturnsTrue(): void {
        $permissionChecker = new PermissionChecker($this->testUser->getId());
        $this->assertTrue($permissionChecker->hasPermission('info'));
        $this->assertTrue($permissionChecker->hasPermission('pages'));
        $this->assertTrue($permissionChecker->hasPermission('files'));
        $this->assertTrue($permissionChecker->hasPermission('design'));
    }

    public function testUserHasPermissionWithUserReturnsTrue(): void {
        $this->assertTrue($this->testUser->hasPermission('info'));
        $this->assertTrue($this->testUser->hasPermission('pages'));
        $this->assertTrue($this->testUser->hasPermission('files'));
        $this->assertTrue($this->testUser->hasPermission('design'));
    }

    public function testUserHasPermissionWithUserReturnsFalse(): void {
        $this->assertFalse($this->testUser->hasPermission('settings_simple'));
        $this->assertFalse($this->testUser->hasPermission('other'));
        $this->assertFalse($this->testUser->hasPermission('audio'));
        $this->assertFalse($this->testUser->hasPermission('non_eixsting_permission'));
    }

    public function testUserGetPermissionCheckerInstanceOfPermissionChecker(): void {
        $this->assertInstanceOf(PermissionChecker::class, $this->testUser->getPermissionChecker());
        $this->assertEquals($this->testUser->getId(), $this->testUser->getPermissionChecker()
            ->getUserId());
    }

    public function testHasPermissionWithUserReturnsFalse(): void {
        $permissionChecker = new PermissionChecker($this->testUser->getId());
        $this->assertFalse($permissionChecker->hasPermission('settings_simple'));
        $this->assertFalse($permissionChecker->hasPermission('other'));
        $this->assertFalse($permissionChecker->hasPermission('audio'));
        $this->assertFalse($permissionChecker->hasPermission('non_eixsting_permission'));
    }

    public function testGetLanguages(): void {
        $permissionChecker = new PermissionChecker($this->testUser->getId());
        $languages = $permissionChecker->getLanguages();
        $language = $languages[0];

        $this->assertEquals(1, count($languages));
        $this->assertEquals('en', $language->getLanguageCode());
    }

    public function testHasPermissionWithoutUser(): void {
        $checker = new PermissionChecker(null);
        $this->assertFalse($checker->hasPermission('info'));
    }

    public function testHasPermissionWithNonExistingUser(): void {
        $checker = new PermissionChecker(PHP_INT_MAX);
        $this->assertFalse($checker->hasPermission('info'));
    }

    public function testNoPerms(): void {
        ob_start();
        noPerms();
        $this->assertStringContainsString(
            'alert alert-dange',
            ob_get_clean()
        );
    }
}
