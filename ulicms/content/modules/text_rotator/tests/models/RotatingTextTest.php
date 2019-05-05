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

        $this->assertEquals("great-animation-1", $text->getAnimation());
        $this->assertEquals("|", $text->getSeparator());
        $this->assertEquals(2500, $text->getSpeed());
        $this->assertEquals("Linux|Apache|PHP|MySQL", $text->getWords());
    }

}
