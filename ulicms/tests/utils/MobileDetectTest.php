<?php

class MobileDetectTest extends \PHPUnit\Framework\TestCase {

    private $no_mobile_design_on_tablet = false;

    protected function setUp(): void {
        $this->no_mobile_design_on_tablet = Settings::get("no_mobile_design_on_tablet");
        Settings::delete("no_mobile_design_on_tablet");
    }

    protected function tearDown(): void {
        if ($this->no_mobile_design_on_tablet) {
            Settings::set("no_mobile_design_on_tablet", 1);
        } else {
            Settings::delete("no_mobile_design_on_tablet");
        }
        unset($_SERVER["HTTP_USER_AGENT"]);
    }

    public function testIsInstalled() {
        $this->assertTrue(class_exists("Mobile_Detect"));
    }

    public function testIsDesktop() {
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";
        $this->assertTrue(is_desktop());

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0";
        $this->assertTrue(is_desktop());

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3";
        $this->assertFalse(is_desktop());

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (PlayBook; U; RIM Tablet OS 1.0.0; en-US) AppleWebKit/534.8+ (KHTML, like Gecko) Version/0.0.1 Safari/534.8+";
        $this->assertFalse(is_desktop());
    }

    public function testIsMobile() {
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";
        $this->assertFalse(is_mobile());
        $this->assertFalse(is_tablet());

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0";
        $this->assertFalse(is_mobile());
        $this->assertFalse(is_tablet());

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3";
        $this->assertTrue(is_mobile());
        $this->assertFalse(is_tablet());

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (PlayBook; U; RIM Tablet OS 1.0.0; en-US) AppleWebKit/534.8+ (KHTML, like Gecko) Version/0.0.1 Safari/534.8+";
        $this->assertTrue(is_mobile());
        $this->assertTrue(is_tablet());
    }

    public function testOptionNoMobileDesignOnTablet() {
        Settings::set("no_mobile_design_on_tablet", 1);

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Linux; U; Android 4.2.2; de-de; A1-811 Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30";
        $this->assertFalse(is_mobile());
        $this->assertTrue(is_tablet());

        Settings::delete("no_mobile_design_on_tablet");
        $this->assertTrue(is_mobile());
        $this->assertTrue(is_tablet());
    }

}
