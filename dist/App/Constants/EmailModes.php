<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Modes for Email delivery
 */
abstract class EmailModes
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
