<?php

declare(strict_types=1);

namespace App\Constants;

class EmailModes
{
    // use mail()
    public const INTERNAL = 'internal';
    // send mails by an external SMTP Server
    public const PHPMAILER = 'phpmailer';
}
