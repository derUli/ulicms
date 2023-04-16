<?php

class OtherSettingsControllerTest extends \PHPUnit\Framework\TestCase
{
    private $defaultSettings = [];

    protected function setUp(): void
    {
        $this->defaultSettings = [
            'email_mode' => Settings::get('email_mode'),
            'domain_to_language' => Settings::get('domain_to_language'),
            'smtp_auth' => Settings::get('smtp_auth'),
            'smtp_encryption' => Settings::get('smtp_encryption'),
            'smtp_no_verify_certificate' => Settings::get('smtp_no_verify_certificate'),
            'twofactor_authentication' => Settings::get('twofactor_authentication'),
            'no_auto_cron' => Settings::get('no_auto_cron'),
            'smtp_host' => Settings::get('smtp_host'),
            'smtp_port' => Settings::get('smtp_port'),
            'max_failed_logins_items' => Settings::get('max_failed_logins_items'),
            'smtp_user' => Settings::get('smtp_user'),
            'smtp_password' => Settings::get('smtp_password')
        ];
    }

    protected function tearDown(): void
    {
        $_POST = [
        ];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePostAllSet(): void
    {
        $mappingLines = [
            'example.de=>de',
            'example.co.uk=>en'
        ];

        $_POST = [
            'email_mode' => 'internal',
            'domain_to_language' => implode("\n", $mappingLines),
            'smtp_auth' => '1',
            'smtp_encryption' => 'ssl',
            'smtp_no_verify_certificate' => '1',
            'twofactor_authentication' => '1',
            'no_auto_cron' => '1',
            'smtp_host' => 'foohost',
            'smtp_port' => '123',
            'max_failed_logins_items' => '5',
            'smtp_user' => 'user',
            'smtp_password' => 'password'
        ];

        $controller = new OtherSettingsController();
        $controller->_savePost();

        $this->assertEquals('internal', Settings::get('email_mode'));
        $this->assertEquals(
            implode("\n", $mappingLines),
            Settings::get('domain_to_language')
        );
        $this->assertEquals('auth', Settings::get('smtp_auth'));
        $this->assertEquals('ssl', Settings::get('smtp_encryption'));
        $this->assertEquals(
            'smtp_no_verify_certificate',
            Settings::get('smtp_no_verify_certificate')
        );
        $this->assertEquals(
            'twofactor_authentication',
            Settings::get('twofactor_authentication')
        );
        $this->assertEquals('no_auto_cron', Settings::get('no_auto_cron'));

        $this->assertEquals('foohost', Settings::get('smtp_host'));
        $this->assertEquals('123', Settings::get('smtp_port'));
        $this->assertEquals(
            '5',
            Settings::get('max_failed_logins_items')
        );
        $this->assertEquals('user', Settings::get('smtp_user'));
        $this->assertEquals('password', Settings::get('smtp_password'));
    }

    public function testSavePostNothingSet(): void
    {
        $_POST = [
        ];

        $controller = new OtherSettingsController();
        $controller->_savePost();

        $this->assertEquals('internal', Settings::get('email_mode'));
        $this->assertEmpty(
            Settings::get('domain_to_language')
        );
        $this->assertNull(Settings::get('smtp_auth'));
        $this->assertEmpty(Settings::get('smtp_encryption'));
        $this->assertNull(Settings::get('smtp_no_verify_certificate'));
        $this->assertNull(Settings::get('twofactor_authentication'));
        $this->assertNull(Settings::get('no_auto_cron'));

        $this->assertEmpty(Settings::get('smtp_host'));
        $this->assertEmpty(Settings::get('smtp_port'));
        $this->assertEmpty(Settings::get('max_failed_logins_items'));
        $this->assertEmpty(Settings::get('smtp_user'));
        $this->assertEmpty(Settings::get('smtp_password'));
    }
}
