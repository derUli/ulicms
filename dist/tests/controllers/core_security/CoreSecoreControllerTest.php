<?php

class CoreSecoreControllerTest extends \PHPUnit\Framework\TestCase
{
    private $initialEnableHsts = false;

    private $initialExpectCt = false;

    protected function setUp(): void
    {
        \App\Storages\Vars::get('http_headers', []);

        $this->initialEnableHsts = Settings::get('enable_hsts');
        $this->initialExpectCt = Settings::get('expect_ct');
    }

    protected function tearDown(): void
    {
        \App\Storages\Vars::get('http_headers', []);

        if ($this->initialEnableHsts) {
            Settings::delete('enable_hsts');
        }
        if ($this->initialExpectCt) {
            Settings::delete('expect_ct');
        }
    }

    public function testController()
    {
        Settings::set('enable_hsts', '1');
        Settings::set('expect_ct', '1');

        $controller = new CoreSecurityController();
        $controller->beforeInit();

        $headers = \App\Storages\Vars::get('http_headers');
        $this->assertContains('X-Frame-Options: SAMEORIGIN', $headers);
        $this->assertContains('X-XSS-Protection: 1', $headers);
        $this->assertContains('X-Content-Type-Options: nosniff', $headers);
        $this->assertContains(
            'Referrer-Policy: no-referrer-when-downgrade',
            $headers
        );
    }
}
