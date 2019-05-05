<?php

class RotatingTextTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::query("delete from {prefix}rotating_text "
                . "where animation like 'great-animation%", true);
    }

    public function testCreateUpdateAndDeleteRotatingText() {
        $text = new RotatingText();
        $text->setAnimation("great-animation-1");
        $text->setSeparator("|");
        $text->setSpeed(2500);
        $text->setWords("Linux|Apache|PHP|MySQL");
        $text->save();

        $savedText = new RotatingText($text->getID());

        $this->assertNotNull($savedText->getID());
        $this->assertEquals("great-animation-1", $savedText->getAnimation());
        $this->assertEquals("|", $savedText->getSeparator());
        $this->assertEquals(2500, $savedText->getSpeed());
        $this->assertEquals("Linux|Apache|PHP|MySQL", $savedText->getWords());

        $savedText->setAnimation("great-animation-2");
        $savedText->setSeparator(";");
        $savedText->setSpeed(1500);
        $savedText->setWords("Pop|Rock|Metal|Electronic|Jazz|Klassik");
        $savedText->save();

        $updatedText = new RotatingText($text->getID());

        $this->assertEquals("great-animation-2", $updatedText->getAnimation());
        $this->assertEquals(";", $updatedText->getSeparator());
        $this->assertEquals(1500, $updatedText->getSpeed());
        $this->assertEquals("Pop|Rock|Metal|Electronic|Jazz|Klassik", $updatedText->getWords());

        $updatedText->delete();

        $deletedText = new RotatingText($text->getID());
        $this->assertNull($deletedText->getId());
    }

}
