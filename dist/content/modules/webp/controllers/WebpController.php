<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;

/**
 * Enables WebP image format
 */
class WebpController extends MainClass {
    public const MODULE_NAME = 'webp';

    /**
     * Just return 'webp' as a string
     *
     * @return string
     */
    public function imageOutputExtensionFilter(string $extension): string {
        return 'webp';
    }
}
