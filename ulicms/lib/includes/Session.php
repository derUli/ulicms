<?php

declare(strict_types=1);

namespace App\Utils\Session;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

function sessionStart(): bool
{
    return !headers_sent() ? session_start() : false;
}

function sessionName(?string $name = null): string
{
    if (!$name) {
        return session_name();
    }

    return !headers_sent() ? session_name($name) : sessionName();
}

function sessionDestroy(): bool
{
    return session_id() ? session_destroy() : false;
}
