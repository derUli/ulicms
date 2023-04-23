<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Targets for HTML Links
 */
abstract class LinkTarget {
    public const TARGET_BLANK = '_blank';

    public const TARGET_SELF = '_self';
}
