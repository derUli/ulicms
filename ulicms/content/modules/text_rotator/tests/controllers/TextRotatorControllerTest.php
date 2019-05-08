<?php

class TextRotatorControllerTest extends \PHPUnit\Framework\TestCase {

    private $testUser;
    private $testGroup;

    public function setUp() {
        @session_start();
        $group = new Group();
        $group->setName("test-group");
        $group->addPermission("text_rotator_edit", true);
        $group->save();

        $this->testGroup = $group;

        $user = new User();
        $user->setUsername("test-" . uniqid());
        $user->setLastname("Doe");
        $user->setFirstName("Johne");
        $user->setPassword(uniqid());
        $user->setGroup($group);
        $user->save();

        $this->testUser = $user;
    }

    public function tearDown() {
        @session_destroy();
        Database::query("delete from {prefix}rotating_text "
                . "where animation like 'great-animation%", true);
        $this->testUser->delete();
        $this->testGroup->delete();

        unset($_SESSION["login_id"]);
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

    public function testSettingsNoEditRight() {

        unset($_SESSION["login_id"]);

        $this->createTestData();
        $count = count(RotatingText::getAll());
        $controller = new TextRotatorController();
        $html = $controller->settings();
        $this->assertStringContainsString("<table", $html);
        $this->assertStringContainsString('<div class="scroll"', $html);
        $this->assertEquals($count + 1, substr_count($html, '<tr>'));
        $this->assertEquals(3, substr_count($html, '</th>'));
        $this->assertEquals($count * 3, substr_count($html, '<td'));
        $this->assertStringNotContainsStringIgnoringCase('<i class="fa fa-plus"></i> New', $html);
    }

    public function testSettingsWithEditRights() {

        $_SESSION["login_id"] = $this->testUser->getId();
        $this->createTestData();
        $count = count(RotatingText::getAll());
        $controller = new TextRotatorController();
        $html = $controller->settings();
        $this->assertStringContainsString("<table", $html);
        $this->assertStringContainsString('<div class="scroll"', $html);
        $this->assertEquals($count + 1, substr_count($html, '<tr>'));
        $this->assertEquals(5, substr_count($html, '</th>'));
        $this->assertEquals($count * 5, substr_count($html, '<td'));
        $this->assertStringContainsStringIgnoringCase('<i class="fa fa-plus"></i> New', $html);
    }

}
