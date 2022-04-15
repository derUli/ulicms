<?php

declare(strict_types=1);

namespace UliCMS\Constants;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

class EmailModes {

    // use mail()
    const INTERNAL = "internal";
    // send mails by an external SMTP Server
    const PHPMAILER = "phpmailer";

}
