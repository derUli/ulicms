<?php

use App\Models\Content\Language;

class LanguageLinkTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::deleteFrom('content', "slug like 'unit_test_%'");
    }

    public function testCreateUpdateAndDeleteLink(): void {
        $link = new Language_Link();
        $link->title = 'Unit Test Link';
        $link->slug = 'unit_test_' . uniqid();
        $link->menu = 'none';
        $link->language = 'de';
        $link->author_id = 1;
        $link->group_id = 1;
        $link->link_to_language = $this->getGermanLanguage()->getId();
        $link->save();

        $id = $link->getID();

        $loadedLink = new Language_Link($id);

        $this->assertIsNumeric($loadedLink->getID());
        $this->assertEquals('Unit Test Link', $loadedLink->title);
        $this->assertStringStartsWith('unit_test_', $loadedLink->slug);
        $this->assertEquals('none', $loadedLink->menu);
        $this->assertEquals($this->getGermanLanguage()->getID(), $loadedLink->link_to_language);
        $this->assertEquals(
            $this->getGermanLanguage()->getID(),
            $loadedLink->link_to_language
        );

        $this->assertEquals('language_link', $loadedLink->type);

        $loadedLink->title = 'Unit Test Updated Link';
        $loadedLink->link_to_language = $this->getEnglishLanguage()->getId();
        $loadedLink->save();

        $loadedLink = new Language_Link($id);

        $this->assertEquals('Unit Test Updated Link', $loadedLink->title);
        $this->assertEquals($this->getEnglishLanguage()->getID(), $loadedLink->link_to_language);
    }

    public function testUpdateCreatesDataset(): void {
        $link = new Language_Link();
        $link->title = 'Unit Test Link';
        $link->slug = 'unit_test_' . uniqid();
        $link->menu = 'none';
        $link->language = 'de';
        $link->author_id = 1;
        $link->group_id = 1;
        $link->link_to_language = $this->getGermanLanguage()->getId();

        $this->assertNull($link->getID());
        $this->assertFalse($link->isPersistent());

        $link->update();

        $this->assertTrue($link->isPersistent());
        $this->assertIsNumeric($link->getID());
    }

    public function testIsRegularReturnsFalse(): void {
        $link = new Language_Link();
        $this->assertFalse($link->isRegular());
    }

    public function testSetAndGetLinkedLanguage(): void {
        $link = new Language_Link();

        $language = new Language();
        $language->loadByLanguageCode('en');

        $this->assertNull($link->getLinkedLanguage());

        $link->setLinkedLanguage($language);
        $this->assertEquals(
            $language->getLanguageCode(),
            $link->getLinkedLanguage()->getLanguageCode()
        );

        $link->setLinkedLanguage(null);
        $this->assertNull($link->getLinkedLanguage());
    }

    private function getGermanLanguage() {
        $language = new Language();
        $language->loadByLanguageCode('de');
        return $language;
    }

    private function getEnglishLanguage() {
        $language = new Language();
        $language->loadByLanguageCode('en');
        return $language;
    }
}
