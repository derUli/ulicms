<?php

declare(strict_types=1);

class CoreSecoreControllerTest extends \PHPUnit\Framework\TestCase {
    private ?bool $initialEnableHsts = false;

    protected function setUp(): void {
        $this->initialEnableHsts = (bool)Settings::get('enable_hsts');
    }

    protected function tearDown(): void {
        if ($this->initialEnableHsts) {
            Settings::delete('enable_hsts');
        }
    }

    public function testController(): void {
        Settings::set('enable_hsts', '1');

        $controller = new CoreSecurityController();
        $controller->beforeInit();

        $headers = \App\Storages\Vars::get('http_headers');
        $this->assertContains('X-Frame-Options: SAMEORIGIN', $headers);
        $this->assertContains('X-Content-Type-Options: nosniff', $headers);
        $this->assertContains('Strict-Transport-Security: max-age=31536000; includeSubDomains', $headers);
    }
}
