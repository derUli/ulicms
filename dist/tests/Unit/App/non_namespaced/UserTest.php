<?php

use App\Constants\HtmlEditor;
use App\Models\Users\GroupCollection;
use App\Models\Users\PasswordReset;
use App\Security\Hash;
use App\Utils\CacheUtil;

class UserTest extends \PHPUnit\Framework\TestCase {
    private $otherGroup;

    protected function setUp(): void {
        CacheUtil::clearAvatars(true);
        $_SERVER['REQUEST_URI'] = '/other-url.html?param=value';

        $_SERVER['REMOTE_ADDR'] = '123.123.123.123';
        require_once getLanguageFilePath('en');

        $user = new User();
        $user->loadByUsername('max_muster');

        if ($user->getId() !== null) {
            $user->delete();
        }

        $group = new Group();
        $group->setName('Other Group');
        $group->save();
        $this->otherGroup = $group;

        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo.html';
    }

    protected function tearDown(): void {
        CacheUtil::clearAvatars(true);
        $_SESSION = [];

        $this->setUp();
        Database::pQuery(
            'delete from `{prefix}groups` '
            . 'where name like ? or name like ?',
            [
                'Other Grou%',
                'Main Group'
            ],
            true
        );
        unset($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL'], $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $_SERVER['HTTPS']);

        $user = $this->getFirstUser();
        $user->setLastAction(0);
    }

    public function testCreateAndDeleteUser(): void {
        $user = new User();
        $user->setUsername('max_muster');
        $user->setFirstname('Max');
        $user->setLastname('Muster');
        $user->setPrimaryGroupId(1);
        $user->setPassword('password123');
        $user->setEmail('max@muster.de');
        $user->setHomepage('http://www.google.de');
        $user->setDefaultLanguage('fr');
        $user->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user->setFailedLogins(0);

        $user->setAboutMe('hello world');
        $lastLogin = time();
        $user->setLastLogin($lastLogin);
        $user->save();
        $this->assertNotNull($user->getId());
        $user = new User();
        $user->loadByUsername('max_muster');
        $this->assertEquals('max_muster', $user->getUsername());
        $this->assertEquals('Max', $user->getFirstname());
        $this->assertEquals('Muster', $user->getLastname());
        $this->assertEquals('Max Muster', $user->getFullName());

        $this->assertEquals('fr', $user->getDefaultLanguage());
        $this->assertEquals('max@muster.de', $user->getEmail());
        $this->assertEquals(1, $user->getPrimaryGroupId());
        $this->assertEquals(1, $user->getPrimaryGroup()
            ->getId());
        $this->assertEquals('Administrator', $user->getPrimaryGroup()
            ->getName());
        $this->assertEquals(
            Hash::hashPassword('password123'),
            $user->getPassword()
        );
        $this->assertEquals($lastLogin, $user->getLastLogin());
        $this->assertEquals('http://www.google.de', $user->getHomepage());
        $this->assertEquals(HtmlEditor::CKEDITOR, $user->getHTMLEditor());
        $this->assertEquals(false, $user->getRequirePasswordChange());
        $this->assertEquals(false, $user->isAdmin());
        $this->assertEquals(false, $user->isLocked());
        $this->assertEquals('hello world', $user->getAboutMe());
        $user->setHTMLEditor(HtmlEditor::CODEMIRROR);
        $user->setRequirePasswordChange(true);
        $user->setLocked(true);
        $user->setAdmin(true);
        $user->setAboutMe('bye');
        $user->setPrimaryGroup($this->otherGroup);
        $this->assertEquals('Other Group', $user->getPrimaryGroup()
            ->getName());
        $this->assertEquals($user->getPrimaryGroupId(), $user->getPrimaryGroup()
            ->getId());
        $user->save();

        $user = new User();
        $user->loadByUsername('max_muster');
        $this->assertEquals(HtmlEditor::CODEMIRROR, $user->getHTMLEditor());

        $this->assertEquals(true, $user->isLocked());
        $this->assertEquals(true, $user->isAdmin());
        $this->assertEquals(true, $user->getRequirePasswordChange());
        $this->assertEquals('bye', $user->getAboutMe());

        // This always returns the URL of an placeholder image
        // since the new avatar feature is not implemented yet
        $this->assertStringEndsWith(
            'content/avatars/77845dbfbccaebb3f1ccd497e9c47466.png',
            $user->getAvatar()
        );

        $user->delete();

        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testLoadByUsernameCaseInsensitive(): void {
        $user = new User();
        $user->setUsername('paul.panzer');
        $user->setLastname('Panzer');
        $user->setFirstname('Paul');
        $user->setPassword('secret');
        $user->setEmail('paul@panzer.de');
        $user->save();

        $savedUser = new User();
        $savedUser->loadByUsername('Paul.Panzer');

        $this->assertEquals('paul.panzer', $savedUser->getUsername());
        $this->assertEquals('Panzer', $savedUser->getLastname());

        $user->delete();
    }

    public function testLoadByEmailCaseInsensitive(): void {
        $user = new User();
        $user->setUsername('paul.panzer');
        $user->setLastname('Panzer');
        $user->setFirstname('Paul');
        $user->setPassword('secret');
        $user->setEmail('paul@panzer.de');
        $user->save();

        $savedUser = new User();
        $savedUser->loadByEmail('Paul@PaNzER.DE');

        $this->assertEquals('paul@panzer.de', $savedUser->getEmail());
        $this->assertEquals('Panzer', $savedUser->getLastname());

        $user->delete();
    }

    public function testGetWelcomeMailText(): void {
        $user = new User();
        $user->setUsername('john.doe');
        $user->setLastname('Doe');
        $user->setFirstname('John');
        $user->setPassword('secret');

        $message = $user->getWelcomeMailText('secret');
        $this->assertStringContainsString(
            'An administrator created a user account for you',
            $message
        );
        $this->assertStringContainsString('Hello John', $message);
        $this->assertStringContainsString('Username: john.doe', $message);
        $this->assertStringContainsString('Password: secret', $message);
    }

    public function testLoadByEmail(): void {
        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPrimaryGroupId(1);
        $user->setPassword('password123');
        $user->setEmail('john@doe.com');
        $user->save();

        $loadedUser = new User();
        $loadedUser->loadByEmail('john@doe.com');

        $this->assertEquals('john@doe.com', $loadedUser->getEmail());
        $this->assertEquals('john-doe', $loadedUser->getUsername());

        $user->delete();
    }

    public function testCheckPasswordReturnsTrue(): void {
        $user = new User();
        $user->setPassword('topsecretpassword');

        $this->assertTrue($user->checkPassword('topsecretpassword'));

        $user->delete();
    }

    public function testCheckPasswordReturnsFalse(): void {
        $user = new User();
        $user->setPassword('topsecretpassword');
        $this->assertFalse($user->checkPassword('falschesPassW0rt'));

        $user->delete();
    }

    public function testFromSessionDataWithInvalidIdReturnsEmptyUser(): void {
        $_SESSION['login_id'] = PHP_INT_MAX;
        $userFromSession = User::fromSessionData();
        $this->assertInstanceOf(User::class, $userFromSession);
        $this->assertNull($userFromSession->getId());
        $this->assertNull($userFromSession->getUsername());
    }

    public function testFromSessionDataWithoutSessionReturnsNull(): void {
        $userFromSession = User::fromSessionData();
        $this->assertNull($userFromSession);
    }

    public function testFromSessionDataReturnsUser(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getLockedUsers(false);
        $user = $users[0];

        $_SESSION['login_id'] = $user->getId();

        $userFromSession = User::fromSessionData();

        $this->assertInstanceOf(User::class, $userFromSession);
        $this->assertEquals($userFromSession->getId(), $user->getId());
        $this->assertEquals(
            $userFromSession->getUsername(),
            $user->getUsername()
        );
        $this->assertEquals(
            $userFromSession->getLastname(),
            $user->getLastname()
        );
    }

    public function testRegisterSessionRegistersSession(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getLockedUsers(false);
        $user = $users[0];

        $user->registerSession();

        $this->assertEquals(get_user_id(), $user->getId());
        $this->assertEquals($user->getUsername(), $_SESSION['ulicms_login']);
        $this->assertNotNull(session_id());
    }

    public function testRegisterSessionThrowError(): void {
        $this->expectException(BadMethodCallException::class);
        $user = new User();
        $user->registerSession();
    }

    public function testToSessionDataReturnsNull(): void {
        $user = new User();
        $this->assertNull($user->toSessionData());
    }

    public function testToSessionDataReturnsArray(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getLockedUsers(false);
        $user = $users[0];
        $sessionData = $user->toSessionData();
        $this->assertIsArray($sessionData);
        $this->assertCount(9, $sessionData);
        $this->assertEquals($user->getId(), $sessionData['login_id']);
        $this->assertEquals($user->getUsername(), $sessionData['ulicms_login']);

        $this->assertEquals($user->getLastname(), $sessionData['lastname']);
    }

    public function testGetAllGroupsReturnsEmptyArray(): void {
        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPassword('password123');
        $user->setEmail('john@doe.com');
        $user->save();

        $this->assertCount(0, $user->getAllGroups());

        $user->delete();
    }

    public function testGetAllGroupsReturnsGroups(): void {
        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPassword('password123');
        $user->setEmail('john@doe.com');

        $group1 = new Group();
        $group1->setName('Main Group');
        $group1->save();

        $group2 = new Group();
        $group2->setName('Other Group 1');
        $group2->save();

        $group3 = new Group();
        $group3->setName('Other Group 2');
        $group3->save();

        $user->setPrimaryGroup($group1);
        $user->setSecondaryGroups([$group2, $group3]);
        $user->save();

        $allGroups = $user->getAllGroups();
        $this->assertCount(3, $allGroups);

        $this->assertEquals(
            $allGroups[0]->getName(),
            'Main Group'
        );
        $this->assertEquals(
            $allGroups[1]->getName(),
            'Other Group 1'
        );
        $this->assertEquals(
            $allGroups[2]->getName(),
            'Other Group 2'
        );

        $user->delete();
    }

    public function testGetPasswordChanged(): void {
        $user = new User();
        $user->setPassword('top-secret');
        $this->assertMatchesRegularExpression(
            '/\d+-\d+-\d+ \d+:\d+:\d+/',
            $user->getPasswordChanged()
        );
    }

    public function testSetAndGetLastAction(): void {
        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPassword('password123');
        $user->setEmail('john@doe.com');
        // does nothing because the dataset is not saved yet
        $user->setLastAction(0);
        $user->save();

        // is zero until the user does something
        $this->assertEquals(0, $user->getLastAction());

        $time = time();
        $user->setLastAction($time);
        $this->assertEquals($time, $user->getLastAction());

        $user->setLastAction(0);
        $this->assertEquals(0, $user->getLastAction());

        $user->delete();
    }

    public function testRemoveSecondaryGroup(): void {
        $group1 = new Group();
        $group1->setName('Group1');
        $group1->setId(123);

        $group2 = new Group();
        $group2->setName('Group2');
        $group2->setId(456);

        $user = new User();
        $user->setSecondaryGroups([$group1, $group2]);

        $this->assertCount(2, $user->getSecondaryGroups());

        $user->removeSecondaryGroup($group1);

        $this->assertCount(1, $user->getSecondaryGroups());
        $this->assertEquals(
            'Group2',
            $user->getSecondaryGroups()[0]->getName()
        );
    }

    public function testSetHtmlEditorToNonSupported(): void {
        $this->expectException(InvalidArgumentException::class);
        $user = new User();

        // there is no "super_editor" so UliCMS sets html_editor
        // to the default value
        $user->setHTMLEditor('super_editor');
    }

    public function testIncreaseAndResetFailedLogins(): void {
        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPassword('password123');
        $user->setEmail('john@doe.com');

        // does nothing because the dataset is not saved yet
        $user->setFailedLogins(3);
        $user->increaseFailedLogins();
        $user->resetFailedLogins();

        $user->save();
        $user->setFailedLogins(1);

        for ($i = 1; $i <= 3; $i++) {
            $user->increaseFailedLogins();
        }

        $this->assertEquals(4, $user->getFailedLogins());

        $user->resetFailedLogins();
        $this->assertEquals(0, $user->getFailedLogins());

        $user->delete();
    }

    public function testResetPassword(): void {
        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPassword('password123');
        $user->setEmail('john@doe.invalid');

        // does nothing because the dataset is not saved yet
        $user->setFailedLogins(3);
        $user->increaseFailedLogins();
        $user->resetFailedLogins();

        $user->saveAndSendMail('quak');

        $passwordReset = new PasswordReset();
        $this->assertCount(
            0,
            $passwordReset->getAllTokensByUserId($user->getId())
        );

        $user->resetPassword();
        $this->assertCount(
            1,
            $passwordReset->getAllTokensByUserId($user->getId())
        );

        $user->delete();
    }

    public function testGetFullNameReturnsFullName(): void {
        $user = new User();
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $this->assertEquals('John Doe', $user->getFullName());
    }

    public function testGetFullNameReturnsEmptyString(): void {
        $user = new User();
        $this->assertEmpty($user->getFullName());
    }

    public function testGetDisplayNameReturnsFullName(): void {
        $user = new User();
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setUsername('johndoe');
        $this->assertEquals('John Doe', $user->getDisplayName());
    }

    public function testGetDisplayNameReturnsUsername(): void {
        $user = new User();
        $user->setUsername('johndoe');
        $this->assertEquals('johndoe', $user->getDisplayName());
    }

    public function testGetDisplayNameReturnsFirstName(): void {
        $user = new User();
        $user->setFirstname('John');
        $user->setUsername('johndoe');
        $this->assertEquals('John', $user->getDisplayName());
    }

    public function testGetDisplayNameReturnsLastName(): void {
        $user = new User();
        $user->setLastname('Doe');
        $user->setUsername('johndoe');
        $this->assertEquals('Doe', $user->getDisplayName());
    }

    public function testGetDisplayNameReturnsEmptyString(): void {
        $user = new User();
        $this->assertEquals('', $user->getFullName());
    }

    public function testGetAvatarReturnsFallback(): void {
        $user = new User();
        $this->assertStringEndsWith(
            'admin/gfx/no_avatar.png',
            $user->getAvatar()
        );
    }

    public function testGetPermissionCheckerReturnsTrue(): void {
        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPassword('password123');
        $user->setEmail('john@doe.invalid');
        $user->setAdmin(true);
        $user->save();

        $permissionChecker = $user->getPermissionChecker();
        $this->assertTrue($permissionChecker->hasPermission('design'));

        $user->delete();
    }

    public function testGetPermissionCheckerReturnsFalse(): void {
        $user = new User();

        $permissionChecker = $user->getPermissionChecker();
        $this->assertFalse($permissionChecker->hasPermission('design'));
    }

    public function testHasPermissionReturnsFalse(): void {
        $user = new User();
        $this->assertFalse($user->hasPermission('design'));
    }

    public function testProcessAvatarReturnsTrue(): void {
        $inputFile = \App\Utils\Path::resolve(
            'ULICMS_ROOT/admin/gfx/apple-touch-icon-120x120.png'
        );

        $user = new User();
        $user->setUsername('john-doe');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setPassword('password123');
        $user->setEmail('john@doe.invalid');
        $user->setAdmin(true);
        $user->save();

        $this->assertStringEndsNotWith(
            '/user-' . $user->getId() . '.png',
            $user->getAvatar()
        );

        $this->assertFalse($user->hasProcessedAvatar());
        $this->assertFalse($user->removeAvatar());
        $user->setAvatar($inputFile);

        $this->assertStringEndsWith(
            'user-' . $user->getId() . '.png',
            $user->getAvatar()
        );

        $this->assertTrue($user->hasProcessedAvatar());
        $this->assertTrue($user->removeAvatar());
        $this->assertFalse($user->hasProcessedAvatar());
    }

    public function testProcessAvatarReturnsFalse(): void {
        $user = new User();
        $this->assertFalse($user->processAvatar('foobar'));
    }

    public function testGetGroupCollection(): void {
        $user = $this->getTestUser();
        $collection = $user->getGroupCollection();
        $this->assertInstanceOf(GroupCollection::class, $collection);

        $this->assertEquals(
            '<div><foo><img><p><span><strong><video>',
            $collection->getAllowableTags()
        );
    }

    public function testIsCurrentReturnsTrue(): void {
        $_SESSION['login_id'] = 123;

        $user = new User();
        $user->setId(123);

        $this->assertTrue($user->isCurrent());
    }

    public function testIsCurrentReturnsFalse(): void {
        $_SESSION['login_id'] = PHP_INT_MAX;

        $user = new User();
        $user->setId(123);

        $this->assertFalse($user->isCurrent());
    }

    public function testIsOnlineReturnsTrue(): void {
        $user = $this->getFirstUser();

        $user->setLastAction(time());

        $this->assertTrue($user->isOnline());
    }

    public function testIsOnlineReturnsFalse(): void {
        $user = $this->getFirstUser();
        $user->setLastAction(12);
        $this->assertFalse($user->isOnline());
    }

    public function testChangeAvatar(): void {
        $user = new User();
        $upload = [
            'tmp_name' => uniqid(),
            'name' => 'foo'
        ];

        $this->assertFalse($user->changeAvatar($upload));
    }

    protected function getTestUser(): User {
        $user = new User();

        $group1 = new Group();
        $group1->setAllowableTags('<p><div><strong><span><img>');

        $group2 = new Group();
        $group2->setAllowableTags('<p><img><foo>');

        $group3 = new Group();
        $group3->setAllowableTags('<video><audio');

        $user->setPrimaryGroup($group1);
        $user->setSecondaryGroups([$group2, $group3]);

        return $user;
    }

    protected function getFirstUser(): User {
        $manager = new \App\Models\Users\UserManager();
        return $manager->getAllUsers()[0];
    }
}
