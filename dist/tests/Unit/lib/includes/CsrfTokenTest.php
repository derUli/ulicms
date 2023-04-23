<?php

class CsrfTokenTest extends \PHPUnit\Framework\TestCase {
    private $initialMinTime;

    protected function setUp(): void {
        $_SESSION = [];
        $_REQUEST = [];

        $this->initialMinTime = Settings::get('min_time_to_fill_form');
    }

    protected function tearDown(): void {
        $_SESSION = [];
        $_REQUEST = [];

        Settings::set('min_time_to_fill_form', $this->initialMinTime);
    }

    public function testCheckCsrfTokenValid(): void {
        $token = get_csrf_token();
        $this->assertNotNull($token);

        $_REQUEST['csrf_token'] = $token;

        $this->assertTrue(check_csrf_token());
    }

    public function testCheckCsrfTokenInvalid(): void {
        $_SESSION['csrf_token'] = 'foo';
        $_REQUEST['csrf_token'] = 'thisisnotthetoken';
        $this->assertFalse(check_csrf_token());
    }

    public function testCheckCsrfTokenNoToken(): void {
        $this->assertFalse(check_csrf_token());
    }

    public function testGetCsrfTokenHtmlWithMinTimeToFillForm(): void {
        Settings::set('min_time_to_fill_form', '6');

        $this->assertStringContainsString(
            '<input type="hidden" name="form_timestamp" value="',
            get_csrf_token_html()
        );
    }

    public function testCsrfTokenHtmlWithMinTimeToFillForm(): void {
        Settings::set('min_time_to_fill_form', '6');

        ob_start();
        csrf_token_html();

        $this->assertStringContainsString(
            '<input type="hidden" name="form_timestamp" value="',
            ob_get_clean()
        );
    }
}
