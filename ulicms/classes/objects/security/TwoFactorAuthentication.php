<?php

declare(strict_types=1);

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

    public function changeSecret(string $secret): void {
        Settings::set("ga_secret", $secret);
    }

    public function generateSecret(): string {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
        $this->changeSecret($secret);
        return $secret;
    }

    public function getCode(): string {
        $ga = new PHPGangsta_GoogleAuthenticator();
        return $ga->getCode($this->getSecret());
    }

    public function checkCode(string $code) {
        return $this->getCode() === $code;
    }

    public static function isEnabled(): bool {
        return boolval(Settings::get("twofactor_authentication"));
    }

}
