<?php

declare(strict_types=1);

namespace App\Constants;

class EmailModes
{

    // use mail()
    const INTERNAL = "internal";
    // send mails by an external SMTP Server
    const PHPMAILER = "phpmailer";
}
