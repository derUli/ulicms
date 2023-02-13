<?php

declare(strict_types=1);

namespace App\Security;

use Settings;

/**
 * This class contains security releated utils
 */
class Encryption
{
    /**
     * Hash user password
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        // TODO: Move password salt from database to CMSConfig
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
