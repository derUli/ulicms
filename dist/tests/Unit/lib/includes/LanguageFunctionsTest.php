<?php

use App\Constants\HtmlEditor;
use App\Models\Content\Language;

class LanguageFunctionsTest extends \PHPUnit\Framework\TestCase {
    private $initialSettingsLanguage;

    protected function setUp(): void {
        $this->initialSettingsLanguage = Settings::get('system_language');
    }

    protected function tearDown(): void {
        Settings::set('system_language', $this->initialSettingsLanguage);

        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }
        }
    }

    public function testGetAllUsedLanguages(): void {
        $languages = getAllUsedLanguages();
        $this->assertGreaterThanOrEqual(2, count($languages));
        $this->assertTrue(in_array('de', $languages));
        $this->assertTrue(in_array('en', $languages));
    }

    public function testGetLanguageNameByCodeReturnsName(): void {
        $this->assertEquals('Deutsch', getLanguageNameByCode('de'));
        $this->assertEquals('English', getLanguageNameByCode('en'));
    }

    public function testGetLanguageNameByCodeReturnsCode(): void {
        $this->assertEquals(
            'gibts_nicht',
            getLanguageNameByCode('gibts_nicht')
        );
    }

    public function testGetAvailableBackendLanguages(): void {
        $this->assertContains('de', getAvailableBackendLanguages());
        $this->assertContains('en', getAvailableBackendLanguages());
    }

    public function testGetSystemLanguageReturnsFrontendLanguageFromSession(): void {
        $_SESSION['language'] = 'en';
        $this->assertEquals('en', getSystemLanguage());
    }

    public function testGetLanguageFilePath(): void {
        $this->assertStringEndsWith('dist/lang/fr.php', getLanguageFilePath('fr'));
    }

    public function testGetAllLanguagesFiltered(): void {
        $language = new Language();
        $language->loadByLanguageCode('en');

        $group = new Group();
        $group->setName('Testgroup');
        $group->setLanguages(
            [
                $language
            ]
        );
        $group->save();

        $user = new User();
        $user->setUsername('testuser-1');
        $user->setPassword(rand_string(23));
        $user->setLastname('Beutlin');
        $user->setFirstname('Bilbo');
        $user->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user->setPrimaryGroup($group);
        $user->save();

        register_session(
            getUserById($user->getId())
        );
        $languages = getAllLanguages(true);

        $this->assertNotContains('de', $languages);
        $this->assertContains('en', $languages);
        $user->delete();
        $group->delete();
    }

    public function testGetAllLanguagesNotFiltered(): void {
        $languages = getAllLanguages();
        $this->assertGreaterThanOrEqual(1, count($languages));

        $languages = getAllLanguages();
        $this->assertGreaterThanOrEqual(1, count($languages));
    }

    public function testGetSystemLanguageReturnsSystemLanguageFromSession(): void {
        $_SESSION['system_language'] = 'de';
        $_SESSION['language'] = 'en';
        $this->assertEquals('de', getSystemLanguage());
    }

    public function testGetSystemLanguageReturnsSystemLanguageFromSetting(): void {
        Settings::set('system_language', 'en');
        $this->assertEquals('en', getSystemLanguage());
    }

    public function testGetSystemLanguageReturnsDe(): void {
        Settings::delete('system_language');

        $this->assertEquals('de', getSystemLanguage());
    }

    public function testgetCurrentLanguageWithCurrentTrue(): void {
        $this->assertNotEmpty(getCurrentLanguage(true));
        $this->assertNotEmpty(getCurrentLanguage(true));
    }
}
