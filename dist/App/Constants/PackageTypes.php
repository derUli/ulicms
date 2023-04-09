<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

/**
 * Type of a package
 */
class PackageTypes
{
    public const TYPE_MODULE = 'module';
    public const TYPE_THEME = 'theme';
}
