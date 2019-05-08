<?php

class TextRotatorControllerTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::query("delete from {prefix}rotating_text "
                . "where animation like 'great-animation%", true);
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

    public function testBeforeContentFilter() {
        $this->createTestData();

        $texts = RotatingText::getAll();

        $input = "";
        foreach ($texts as $text) {
            $input .= "Foo " . $text->getShortcode() . " Bar<br/>";
        }

        $processed = apply_filter($input, "before_content");

        foreach ($texts as $text) {
            $this->assertStringContainsString($text->getHtml(), $processed);
        }
    }

    public function testGetAnimationItems() {
        $controller = new TextRotatorController();
        $items = $controller->getAnimationItems();
        $this->assertTrue(is_array($items));
        $this->assertCount(37, $items);
    }

}
