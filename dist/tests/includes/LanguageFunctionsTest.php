<?php

use App\Models\Content\Language;
use App\Constants\HtmlEditor;

class LanguageFunctionsTest extends \PHPUnit\Framework\TestCase
{
    private $initialSettingsLanguage;

    protected function setUp(): void
    {
        $this->initialSettingsLanguage = Settings::get("system_language");
    }

    protected function tearDown(): void
    {
        Settings::set('system_language', $this->initialSettingsLanguage);

        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }
        }
    }

    public function testGetAllUsedLanguages()
    {
        $languages = getAllUsedLanguages();
        $this->assertGreaterThanOrEqual(2, count($languages));
        $this->assertTrue(in_array('de', $languages));
        $this->assertTrue(in_array('en', $languages));
    }

    public function testGetPreferredLanguage()
    {
        $acceptLanguageHeader1 = "Accept-Language: da, en - gb;
        q = 0.8, en;
        q = 0.7, de;
        q = 0.5";
        $this->assertEquals('en', get_prefered_language(['de', 'en'], $acceptLanguageHeader1));

        $acceptLanguageHeader2 = "Accept-Language: da, en - gb;
        q = 0.8, en;
        q = 0.7, de;
        q = 0.9";
        $this->assertEquals('de', get_prefered_language(['de', 'en'], $acceptLanguageHeader2));
    }

    public function testGetLanguageNameByCodeReturnsName()
    {
        $this->assertEquals("Deutsch", getLanguageNameByCode('de'));
        $this->assertEquals("English", getLanguageNameByCode('en'));
    }

    public function testGetLanguageNameByCodeReturnsCode()
    {
        $this->assertEquals(
            "gibts_nicht",
            getLanguageNameByCode("gibts_nicht")
        );
    }

    public function testGetAvailableBackendLanguages()
    {
        $this->assertContains('de', getAvailableBackendLanguages());
        $this->assertContains('en', getAvailableBackendLanguages());
    }

    public function testGetSystemLanguageReturnsFrontendLanguageFromSession()
    {
        $_SESSION['language'] = 'en';
        $this->assertEquals('en', getSystemLanguage());
    }

    public function testGetLanguageFilePath()
    {
        $this->assertStringEndsWith('dist/lang/fr.php', getLanguageFilePath('fr'));
    }

    public function testGetAllLanguagesFiltered()
    {
        $language = new Language();
        $language->loadByLanguageCode('en');

        $group = new Group();
        $group->setName("Testgroup");
        $group->setLanguages(
            [
                $language
            ]
        );
        $group->save();

        $user = new User();
        $user->setUsername("testuser-1");
        $user->setPassword(rand_string(23));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
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

    public function testGetAllLanguagesNotFiltered()
    {
        $languages = getAllLanguages();
        $this->assertGreaterThanOrEqual(1, count($languages));

        $languages = getAllLanguages();
        $this->assertGreaterThanOrEqual(1, count($languages));
    }

    public function testGetSystemLanguageReturnsSystemLanguageFromSession()
    {
        $_SESSION["system_language"] = 'de';
        $_SESSION['language'] = 'en';
        $this->assertEquals('de', getSystemLanguage());
    }

    public function testGetSystemLanguageReturnsSystemLanguageFromSetting()
    {
        Settings::set("system_language", 'en');
        $this->assertEquals('en', getSystemLanguage());
    }

    public function testGetSystemLanguageReturnsDe()
    {
        Settings::delete("system_language");

        $this->assertEquals('de', getSystemLanguage());
    }

    public function testgetCurrentLanguageWithCurrentTrue()
    {
        $this->assertNotEmpty(getCurrentLanguage(true));
        $this->assertNotEmpty(getCurrentLanguage(true));
    }
}
