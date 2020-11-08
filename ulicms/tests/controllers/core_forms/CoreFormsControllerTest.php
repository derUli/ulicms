<?php

class CoreFormsControllerTest extends \PHPUnit\Framework\TestCase {

    private $defaultSettings = [];

    protected function setUp(): void {
        $this->defaultSettings = [
            "country_blacklist" => Settings::get("country_blacklist"),
            "reject_requests_from_bots" => Settings::get("reject_requests_from_bots")
        ];
        require_once getLanguageFilePath("en");
        Translation::loadAllModuleLanguageFiles("en");
    }

    protected function tearDown(): void {
        $_POST = [];
        $_SERVER = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
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

    public function testWithForbiddenCountry() {
        Settings::set("country_blacklist", "vn,jp,at,tr");

        $_POST = [
            "foo" => "bar",
            "hello" => "world"
        ];
        $_SERVER["REMOTE_ADDR"] = "123.30.54.106";
        $controller = new CoreFormsController();
        $this->assertStringContainsString(
                "Access to this function for your country is blocked",
                $controller->_spamCheck()
        );
    }

    public function testWithBot() {
        Settings::set("reject_requests_from_bots", "1");

        $_POST = [
            "foo" => "bar",
            "hello" => "world"
        ];
        $_SERVER['HTTP_USER_AGENT'] = "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";

        $controller = new CoreFormsController();
        $this->assertStringContainsString(
                "Bots are not allowed to send messages on this website.",
                $controller->_spamCheck()
        );
    }

}
