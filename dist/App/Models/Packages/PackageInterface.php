<?php

declare(strict_types=1);

namespace App\Models\Packages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

interface PackageInterface {
    /**
     * Check if package is installed
     *
     * @return bool
     */
    public function isInstalled(): bool;

    /**
     * Uninstall a package
     *
     * @return bool
     */
    public function uninstall(): bool;
}
