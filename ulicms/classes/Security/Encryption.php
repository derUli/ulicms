<?php

declare(strict_types=1);

namespace UliCMS\Security;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Settings;

class Encryption {

    // use this to encrypt user passwords
    public static function hashPassword(string $password): string {
        $salt = Settings::get("password_salt");

        // if no salt is set, generate it
        if (!$salt) {
            $newSalt = uniqid();
            Settings::set("password_salt", $newSalt);
            $salt = $newSalt;
        }
        return hash("sha512", $salt . $password);
    }

}
