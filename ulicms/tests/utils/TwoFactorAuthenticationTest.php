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
            "twofactor_authentication"
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

}
