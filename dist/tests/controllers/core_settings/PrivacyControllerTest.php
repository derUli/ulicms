<?php

class PrivacyControllerTest extends \PHPUnit\Framework\TestCase
{
    private $defaultSettings = [];

    protected function setUp(): void
    {
        $this->defaultSettings = [
            "privacy_policy_checkbox_enable_en" => Settings::get("privacy_policy_checkbox_enable_en"),
            "log_ip" => Settings::get("log_ip"),
            "delete_ips_after_48_hours" => Settings::get("delete_ips_after_48_hours"),
            "keep_spam_ips" => Settings::get("keep_spam_ips"),
            "privacy_policy_checkbox_text_en" => Settings::get("privacy_policy_checkbox_text_en")
        ];
    }

    protected function tearDown(): void
    {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePostAllSet(): void
    {
        $_POST = [
            'language' => 'en',
            "privacy_policy_checkbox_enable" => "1",
            "log_ip" => "1",
            "delete_ips_after_48_hours" => "1",
            "keep_spam_ips" => 1,
            "privacy_policy_checkbox_text" => "Hello World"
        ];

        $controller = new PrivacyController();
        $controller->_savePost();

        $this->assertEquals(
            "1",
            Settings::get("privacy_policy_checkbox_enable_en")
        );

        $this->assertEquals(
            "log_ip",
            Settings::get("log_ip")
        );

        $this->assertEquals(
            "delete_ips_after_48_hours",
            Settings::get("delete_ips_after_48_hours")
        );

        $this->assertEquals(
            "1",
            Settings::get("keep_spam_ips")
        );

        $this->assertEquals(
            "Hello World",
            Settings::get("privacy_policy_checkbox_text_en")
        );
    }

    public function testSavePostNothingSet(): void
    {
        $_POST = [
            'language' => 'en'
        ];

        $controller = new PrivacyController();
        $controller->_savePost();

        $this->assertNull(
            Settings::get("privacy_policy_checkbox_enable_en")
        );

        $this->assertNull(
            Settings::get("log_ip")
        );

        $this->assertNull(
            Settings::get("delete_ips_after_48_hours")
        );

        $this->assertNull(
            Settings::get("keep_spam_ips")
        );

        $this->assertEquals(
            "",
            Settings::get("privacy_policy_checkbox_text_en")
        );
    }
}
