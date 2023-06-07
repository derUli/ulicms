<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Imagine\Gd\Imagine;
use Imagine\Image\AbstractImagine;

/**
 * Helper methods for Imagine
 * @since 2023.2 Use always GD
 */
abstract class ImagineHelper extends Helper {
    public const ACCEPT_MIMES = 'image/jpeg,image/png,image/gif';

    /**
     * Get Imagine instance
     * @return AbstractImagine
     */
    public static function getImagine(): AbstractImagine {
        return new Imagine();
    }
}
