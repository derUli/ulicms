<?php

declare(strict_types=1);

namespace UliCMS\Utils;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

class Session {

    public static function sessionStart(): bool {
        return !headers_sent() ? session_start() : false;
    }

    public static function sessionName(?string $name = null): string {
        if (!$name) {
            return session_name();
        }

        return !headers_sent() ? session_name($name) : self::sessionName();
    }

    public static function sessionDestroy(): bool {
        return session_id() ? session_destroy() : false;
    }

}
