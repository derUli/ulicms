<?php

class LanguageTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        $this->tearDown();
    }

    public function tearDown()
    {
        $sql = "delete from `{prefix}languages` where language_code <> 'de' and language_code <> 'en'";
        Database::query($sql, true);
        Settings::set("default_language", "de");
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

    public function testDefaultLanguage()
    {
        $lang = new Language();
        $lang->loadByLanguageCode("de");
        $this->assertTrue($lang->isDefaultLanguage());
        $lang->loadByLanguageCode("en");
        $this->assertFalse($lang->isDefaultLanguage());
        $lang->makeDefaultLanguage();
        $this->assertTrue($lang->isDefaultLanguage());
    }
}