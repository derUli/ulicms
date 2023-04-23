<?php

use App\Security\TwoFactorAuthentication;

class TwoFactorAuthenticationTest extends \PHPUnit\Framework\TestCase {
    private $initialSettings = [];

    protected function setUp(): void {
        $settings = [
            'twofactor_authentication',
            'ga_secret'
        ];
        foreach ($settings as $setting) {
            $this->initialSettings[$setting] = Settings::get($setting);
        }
    }

    protected function tearDown(): void {
        TwoFactorAuthentication::disable();

        foreach ($this->initialSettings as $key => $value) {
            if ($value === null) {
                Settings::delete($key);
            } else {
                Settings::set($key, $value);
            }
        }
    }

    public function testIsEnabledReturnsTrue(): void {
        Settings::set('twofactor_authentication', 'twofactor_authentication');
        $this->assertTrue(TwoFactorAuthentication::isEnabled());
    }

    public function testIsEnabledReturnsFalse(): void {
        Settings::delete('twofactor_authentication');
        $this->assertFalse(TwoFactorAuthentication::isEnabled());
    }

    public function testConstructor(): void {
        Settings::set('twofactor_authentication', 'twofactor_authentication');
        Settings::delete('ga_secret');
        $this->assertNull(Settings::get('ga_secret'));

        $auth = new TwoFactorAuthentication();
        $this->assertIsString(Settings::get('ga_secret'));
        $this->assertEquals(Settings::get('ga_secret'), $auth->getSecret());
    }

    public function testGenerateSecret(): void {
        $auth = new TwoFactorAuthentication();
        $oldSecret = $auth->getSecret();

        $changedSecret = $auth->generateSecret();

        $this->assertNotEquals($oldSecret, $changedSecret);
    }

    public function testGetSecret(): void {
        $auth = new TwoFactorAuthentication();
        $this->assertIsString($auth->getSecret());
        $this->assertEquals(Settings::get('ga_secret'), $auth->getSecret());
    }

    public function testGetCode(): void {
        Settings::set('twofactor_authentication', 'twofactor_authentication');
        $auth = new TwoFactorAuthentication();

        $code = $auth->getCode();
        $this->assertIsNumeric($code);
        $this->assertEquals(6, strlen($code));
    }

    public function testCheckCodeReturnsTrue(): void {
        Settings::set('twofactor_authentication', 'twofactor_authentication');
        $auth = new TwoFactorAuthentication();
        $code = $auth->getCode();

        $this->assertTrue($auth->checkCode($code));
    }

    public function testCheckCodeReturnsFalse(): void {
        Settings::set('twofactor_authentication', 'twofactor_authentication');
        $auth = new TwoFactorAuthentication();

        $this->assertFalse($auth->checkCode('123456'));
    }

    public function testEnable(): void {
        Settings::delete('twofactor_authentication');

        $this->assertFalse(TwoFactorAuthentication::isEnabled());
        TwoFactorAuthentication::enable();
        $this->assertTrue(TwoFactorAuthentication::isEnabled());
    }

    public function testDisable(): void {
        Settings::delete('twofactor_authentication');

        TwoFactorAuthentication::enable();
        TwoFactorAuthentication::disable();

        $this->assertFalse(TwoFactorAuthentication::isEnabled());
    }

    public function testToggle(): void {
        Settings::delete('twofactor_authentication');

        $this->assertFalse(TwoFactorAuthentication::isEnabled());

        TwoFactorAuthentication::toggle();
        $this->assertTrue(TwoFactorAuthentication::isEnabled());

        TwoFactorAuthentication::toggle();
        $this->assertFalse(TwoFactorAuthentication::isEnabled());

        TwoFactorAuthentication::toggle();
        $this->assertTrue(TwoFactorAuthentication::isEnabled());
    }
}
