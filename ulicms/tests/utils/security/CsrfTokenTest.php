<?php

class CsrfTokenTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        $_SESSION = [];
        $_REQUEST = [];
    }

    protected function tearDown(): void {
        $_SESSION = [];
        $_REQUEST = [];
    }

    public function testCheckCsrfTokenValid() {
        $token = get_csrf_token();
        $this->assertNotNull($token);

        $_REQUEST["csrf_token"] = $token;

        $this->assertTrue(check_csrf_token());
    }

    public function testCheckCsrfTokenInvalid() {
        $token = get_csrf_token();
        $_REQUEST["csrf_token"] = "thisisnotthetoken";
        $this->assertFalse(check_csrf_token());
    }

    public function testCheckCsrfTokenNoToken() {
        $this->assertFalse(check_csrf_token());
    }

    public function testGetCsrfTokenHtmlWithMinTimeToFillForm() {
        $initialMinTime = Settings::get("min_time_to_fill_form");

        Settings::set("min_time_to_fill_form", "6");

        $this->assertStringContainsString(
                '<input type="hidden" name="form_timestamp" value="',
                get_csrf_token_html()
        );
        Settings::set("min_time_to_fill_form", $initialMinTime);
    }

    public function testCsrfTokenHtmlWithMinTimeToFillForm() {
        $initialMinTime = Settings::get("min_time_to_fill_form");

        Settings::set("min_time_to_fill_form", "6");

        ob_start();
        csrf_token_html();

        $this->assertStringContainsString(
                '<input type="hidden" name="form_timestamp" value="',
                ob_get_clean()
        );
        Settings::set("min_time_to_fill_form", $initialMinTime);
    }

}
