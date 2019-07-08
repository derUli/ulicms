<?php

namespace UliCMS\Security;

use Settings;

/**
 * Description of TwoFactorAuthentication
 *
 * @author deruli
 */
class TwoFactorAuthentication {

    public static function isEnabled() {
        return boolval(Settings::get("twofactor_authentication"));
    }

}
