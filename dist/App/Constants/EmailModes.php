<?php

declare(strict_types=1);

namespace App\Constants;

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
