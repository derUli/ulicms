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
        $this->assertEquals("[rotating_text={$savedText->getID()}]", $savedText->getShortcode());


        $html = $text->getHtml();

        $this->assertStringContainsString('class="text-rotator"', $html);
        $this->assertStringContainsString('data-animation="great-animation-1"', $html);
        $this->assertStringContainsString('data-speed="2500"', $html);
        $this->assertStringContainsString('data-separator="|"', $html);
        $this->assertStringContainsString("Linux|Apache|PHP|MySQL", $html);


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

    private function createTestData() {
        for ($i = 0; $i <= 3; $i++) {
            $text = new RotatingText();
            $text->setAnimation("great-animation-{$i}");
            $text->setSeparator("|");
            $text->setSpeed(2500);
            $text->setWords("Linux|Apache|PHP|MySQL");
            $text->save();
        }
    }

    public function testGetAll() {
        $this->createTestData();
        $texts = RotatingText::getAll();
        $this->assertGreaterThanOrEqual(3, count($texts));
        foreach ($texts as $text) {
            $this->assertInstanceOf(RotatingText::class, $text);
            $this->assertNotNull($text->getID());
            $this->assertStringStartsWith("great-animation-",
                    $text->getAnimation());
        }
    }

}
