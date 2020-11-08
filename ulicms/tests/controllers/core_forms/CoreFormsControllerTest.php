<?php

class CoreFormsControllerTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {

        require_once getLanguageFilePath("en");
        Translation::loadAllModuleLanguageFiles("en");
    }

    protected function tearDown(): void {
        $_POST = [];
    }

    public function testIncSpamCount() {
        $controller = new CoreFormsController();
        $initialCount = intval(Settings::get("contact_form_refused_spam_mails"));

        for ($i = 1; $i <= 3; $i++) {
            $oldCount = intval(Settings::get("contact_form_refused_spam_mails"));
            $newCount = $controller->_incSpamCount();
            $this->assertIsInt($newCount);
            $this->assertGreaterThan($oldCount, $newCount);
        }

        $this->assertEquals(
                $initialCount + 3,
                intval(Settings::get("contact_form_refused_spam_mails"))
        );
    }

    public function testSpamCheckReturnsNull() {
        $_POST = [
            "foo" => "bar",
            "hello" => "world"
        ];
        $controller = new CoreFormsController();
        $this->assertNull($controller->_spamCheck());
    }

    public function testSpamCheckWithHoneypotFilled() {
        $_POST = [
            "foo" => "bar",
            "hello" => "world",
            "my_homepage_url" => "http://hacker.ru"
        ];
        $controller = new CoreFormsController();
        $this->assertEquals("Honeypot is not empty!", $controller->_spamCheck());
    }

    public function testWithBadWord() {
        $_POST = [
            "foo" => "bar",
            "hello" => "world",
            "spam" => "Viagra"
        ];
        $controller = new CoreFormsController();
        $this->assertEquals("The request contains a bad word: viagra", $controller->_spamCheck());
    }

}
