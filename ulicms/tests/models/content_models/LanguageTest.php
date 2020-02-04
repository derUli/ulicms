<?php

use UliCMS\Models\Content\Language;

class LanguageTest extends \PHPUnit\Framework\TestCase {

	private $initialDefaultLanguage;

	public function setUp() {
		$this->initialDefaultLanguage = Settings::get("default_language");
		$this->tearDown();

		Settings::set("default_language", "de");
	}

	public function tearDown() {
		unset($_SESSION["language"]);

		$sql = "delete from `{prefix}languages` where language_code <> 'de' and language_code <> 'en'";
		Database::query($sql, true);

		Settings::set("default_language", $this->initialDefaultLanguage);
	}

	public function testGetAllLanguages() {
		$list = Language::getAllLanguages();
		$this->assertEquals(2, count($list));
	}

	public function testLoadLanguage() {
		$lang = new Language();
		$this->assertNull($lang->getId());
		$this->assertNull($lang->getName());
		$this->assertNull($lang->getLanguageCode());
		$lang->loadByLanguageCode("de");
		$this->assertNotNull($lang->getID());
		$this->assertEquals("de", $lang->getLanguageCode());
		$this->assertEquals("Deutsch", $lang->getName());
	}

	public function testCreateLanguage() {
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

	public function testIsDefaultLanguage() {
		$lang = new Language();
		$lang->loadByLanguageCode("de");
		$this->assertTrue($lang->isDefaultLanguage());
		$lang->loadByLanguageCode("en");
		$this->assertFalse($lang->isDefaultLanguage());
		$lang->makeDefaultLanguage();
		$this->assertTrue($lang->isDefaultLanguage());
	}

	public function testIsCurrentLanguageReturnsTrue() {
		$_SESSION["language"] = "de";
		$lang = new Language();
		$lang->loadByLanguageCode("de");
		$this->assertTrue($lang->isCurrentLanguage());
	}

	public function testIsCurrentLanguageReturnsFalse() {
		$_SESSION["language"] = "de";
		$lang = new Language();
		$lang->loadByLanguageCode("en");
		$this->assertFalse($lang->isCurrentLanguage());
	}

	public function testToString() {
		$lang = new Language();
		$lang->setLanguageCode("fr");
		$this->assertEquals("fr", strval($lang));
	}

	public function testGetLanguageLink() {

		$lang = new Language();
		$lang->setLanguageCode("fr");
		$this->assertEquals("./?language=fr", $lang->getLanguageLink());
	}

	public function testFillVars() {
		$lang = new Language();
		$lang->fillVars(null);
		$this->assertNull($lang->getLanguageCode());
	}

}
