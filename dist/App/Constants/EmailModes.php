<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

/**
 * Modes for Email delivery
 */
class EmailModes
{
    /**
     * Uses mail()
     */
    public const INTERNAL = 'internal';

    /**
     * Uses phpmailer
     */
    public const PHPMAILER = 'phpmailer';
}
