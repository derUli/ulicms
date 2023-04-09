<?php

declare(strict_types=1);

namespace App\Security;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use Settings;
use PHPGangsta_GoogleAuthenticator;

// two factor authentication based by Google Authenticator
// and PHPGangstas implementation of Google Authenticator in PHP
class TwoFactorAuthentication
{
    public function __construct()
    {
        if (!$this->getSecret()) {
            $this->generateSecret();
        }
    }

    public function getSecret(): ?string
    {
        return Settings::get("ga_secret");
    }

    public function changeSecret(string $secret): void
    {
        Settings::set("ga_secret", $secret);
    }

    public function generateSecret(): string
    {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
        $this->changeSecret($secret);
        return $secret;
    }

    public function getCode(): string
    {
        $ga = new PHPGangsta_GoogleAuthenticator();
        return $ga->getCode($this->getSecret());
    }

    public function checkCode(?string $code): bool
    {
        return $this->getCode() === $code;
    }

    // is two factor authentication enabled?
    public static function isEnabled(): bool
    {
        return (bool) Settings::get("twofactor_authentication");
    }

    public static function enable(): void
    {
        Settings::set("twofactor_authentication", "1");
    }

    public static function disable(): void
    {
        Settings::delete("twofactor_authentication");
    }

    public static function toggle(): void
    {
        self::isEnabled() ? self::disable() : self::enable();
    }
}
