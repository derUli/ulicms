<?php

declare(strict_types=1);

namespace App\Utils\Session;

function sessionStart(): bool
{
    return !headers_sent() && !session_id() ? session_start() : false;
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
