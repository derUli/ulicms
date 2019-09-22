<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TwoFactorAuthenticationTest
 *
 * @author deruli
 */
use UliCMS\Security\TwoFactorAuthentication;

class TwoFactorAuthenticationTest extends \PHPUnit\Framework\TestCase {

    private $initialSettings = [];

    public function setUp() {
        $settings = [
            "twofactor_authentication",
            "ga_secret"
        ];
        foreach ($settings as $setting) {
            $this->initialSettings[$setting] = Settings::get($setting);
        }
    }

    public function tearDown() {
        foreach ($this->initialSettings as $key => $value) {
            if ($value === false) {
                Settings::delete($key);
            } else {
                Settings::set($key, $value);
            }
        }
    }

    public function testIsEnabledReturnsTrue() {
        Settings::set("twofactor_authentication", "twofactor_authentication");
        $this->assertTrue(TwoFactorAuthentication::isEnabled());
    }

    public function testIsEnabledReturnsFalse() {
        Settings::delete("twofactor_authentication");
        $this->assertFalse(TwoFactorAuthentication::isEnabled());
    }

    public function testConstructor() {
        Settings::set("twofactor_authentication", "twofactor_authentication");
        Settings::delete("ga_secret");
        $this->assertNull(Settings::get("ga_secret"));

        $auth = new TwoFactorAuthentication();
        $this->assertIsString(Settings::get("ga_secret"));
        $this->assertEquals(Settings::get("ga_secret"), $auth->getSecret());
    }

    public function testGenerateSecret() {
        $auth = new TwoFactorAuthentication();
        $oldSecret = $auth->getSecret();

        $changedSecret = $auth->generateSecret();

        $this->assertNotEquals($oldSecret, $changedSecret);
    }

    public function testGetSecret() {
        $auth = new TwoFactorAuthentication();
        $this->assertIsString($auth->getSecret());
        $this->assertEquals(Settings::get("ga_secret"), $auth->getSecret());
    }

    public function testGetCode() {
        Settings::set("twofactor_authentication", "twofactor_authentication");
        $auth = new TwoFactorAuthentication();

        $code = $auth->getCode();
        $this->assertIsNumeric($code);
        $this->assertEquals(6, strlen($code));
    }

    public function testCheckCodeReturnsTrue() {
        Settings::set("twofactor_authentication", "twofactor_authentication");
        $auth = new TwoFactorAuthentication();
        $code = $auth->getCode();

        $this->assertTrue($auth->checkCode($code));
    }

    public function testCheckCodeReturnsFalse() {
        Settings::set("twofactor_authentication", "twofactor_authentication");
        $auth = new TwoFactorAuthentication();

        $this->assertFalse($auth->checkCode("123456"));
    }

}
