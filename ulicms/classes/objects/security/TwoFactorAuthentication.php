<?php

namespace UliCMS\Security;

use Settings;
use PHPGangsta_GoogleAuthenticator;

/**
 * Description of TwoFactorAuthentication
 *
 * @author deruli
 */
class TwoFactorAuthentication {

    public function __construct() {
        if (!$this->getSecret()) {
            $this->generateSecret();
        }
    }

    public function getSecret() {
        return Settings::get("ga_secret");
    }

    public function changeSecret($secret) {
        Settings::set("ga_secret", $secret);
    }

    public function generateSecret() {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
        $this->changeSecret($secret);
        return $secret;
    }

    public function getCode() {
        $ga = new PHPGangsta_GoogleAuthenticator();
        return $ga->getCode($this->getSecret());
    }

    public function checkCode($code) {
        return $this->getCode() === $code;
    }

    public static function isEnabled() {
        return boolval(Settings::get("twofactor_authentication"));
    }

}
