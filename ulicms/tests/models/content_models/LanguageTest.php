<?php

use App\Models\Content\Language;

class LanguageTest extends \PHPUnit\Framework\TestCase
{
    private $initialDefaultLanguage;
    private $initialDomain2LanguageMapping = null;

    protected function setUp(): void
    {
        $this->initialDefaultLanguage = Settings::get("default_language");

        Settings::set("default_language", "de");

        $_SESSION = [];
        $this->initialDomain2LanguageMapping = Settings::get("domain_to_language");
    }

    protected function tearDown(): void
    {
        $_SESSION = [];

        $sql = "delete from `{prefix}languages` where language_code <> 'de' and language_code <> 'en'";
        Database::query($sql, true);
        Settings::set("default_language", $this->initialDefaultLanguage);

        Settings::set("domain_to_language", $this->initialDomain2LanguageMapping);
    }

    public function testGetAllLanguages()
    {
        $list = Language::getAllLanguages();
        $this->assertEquals(2, count($list));
    }

    public function testLoadLanguage()
    {
        $lang = new Language();
        $this->assertNull($lang->getId());
        $this->assertNull($lang->getName());
        $this->assertNull($lang->getLanguageCode());
        $lang->loadByLanguageCode("de");
        $this->assertNotNull($lang->getID());
        $this->assertEquals("de", $lang->getLanguageCode());
        $this->assertEquals("Deutsch", $lang->getName());
    }

    public function testCreateLanguage()
    {
        $lang = new Language();
        $lang->setName("Lampukisch");
        $lang->setLanguageCode("lp");
        $lang->save();
        $lang = new Language();
        $lang->loadByLanguageCode("lp");
        $this->assertNotNull($lang->getId());
        $this->assertEquals("Lampukisch", $lang->getName());
        $this->assertEquals("lp", $lang->getLanguageCode());
        $this->assertNotNull($lang->getID());
        $lang->setName("Klingonisch");
        $lang->setLanguageCode("kg");
        $lang->save();
        $lang = new Language();
        $lang->loadByLanguageCode("kg");
        $this->assertNotNull($lang->getId());
        $this->assertEquals("Klingonisch", $lang->getName());
        $this->assertEquals("kg", $lang->getLanguageCode());
        $id = $lang->getId();
        $lang = new Language($id);
        $this->assertEquals($id, $lang->getID());
        $this->assertEquals("Klingonisch", $lang->getName());
        $this->assertEquals("kg", $lang->getLanguageCode());
        $lang->delete();
        $this->assertNull($lang->getID());
    }

    public function testIsDefaultLanguage()
    {
        $lang = new Language();
        $lang->loadByLanguageCode("de");
        $this->assertTrue($lang->isDefaultLanguage());
        $lang->loadByLanguageCode("en");
        $this->assertFalse($lang->isDefaultLanguage());
        $lang->makeDefaultLanguage();
        $this->assertTrue($lang->isDefaultLanguage());
    }

    public function testIsCurrentLanguageReturnsTrue()
    {
        $_SESSION["language"] = "de";
        $lang = new Language();
        $lang->loadByLanguageCode("de");
        $this->assertTrue($lang->isCurrentLanguage());
    }

    public function testIsCurrentLanguageReturnsFalse()
    {
        $_SESSION["language"] = "de";
        $lang = new Language();
        $lang->loadByLanguageCode("en");
        $this->assertFalse($lang->isCurrentLanguage());
    }

    public function testToString()
    {
        $lang = new Language();
        $lang->setLanguageCode("fr");
        $this->assertEquals("fr", strval($lang));
    }

    public function testGetLanguageLinkReturnsRelative()
    {
        $lang = new Language();
        $lang->setLanguageCode("fr");
        $this->assertEquals("./?language=fr", $lang->getLanguageLink());
    }

    public function testGetLanguageLinkReturnsAbsolute()
    {
        $mappingLines = [
            'example.de=>de',
            'example.co.uk=>en'
        ];
        Settings::set(
            "domain_to_language",
            implode("\n", $mappingLines)
        );

        $lang = new Language();
        $lang->setLanguageCode("en");
        $this->assertEquals("http://example.co.uk", $lang->getLanguageLink());
    }

    public function testFillVars()
    {
        $lang = new Language();
        $lang->fillVars(null);
        $this->assertNull($lang->getLanguageCode());
    }
}
